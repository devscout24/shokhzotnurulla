<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dealership\Dealer;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use App\Enums\DealerStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class DealerController extends Controller
{
    public function index()
    {
        $dealers = Dealer::latest()->paginate(15);
        return view('admin.pages.dealers.index', compact('dealers'));
    }

    public function create()
    {
        return view('admin.pages.dealers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:50',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:16',
            'domains' => 'required|array|min:1',
            'domains.*' => 'required|string|unique:domains,domain',
        ]);

        $dealerData = [
            'name' => $validated['company_name'],
            'company_name' => $validated['company_name'],
            'slug' => Str::slug($validated['company_name']),
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'status' => DealerStatus::INACTIVE,
            'is_active' => false,
            'domain' => $validated['domains'][0], // Set the first one as the primary on dealer table too
        ];

        $dealer = Dealer::create($dealerData);

        foreach ($validated['domains'] as $index => $domainName) {
            $dealer->domains()->create([
                'domain' => $domainName,
                'is_primary' => $index === 0,
            ]);
        }

        $user = User::create([
            'first_name' => $validated['company_name'],
            'last_name' => 'Admin',
            'email' => $validated['email'],
            'password' => Hash::make(Str::random(16)),
            'is_system_user' => false,
            'current_dealer_id' => $dealer->id,
        ]);

        $dealer->owners()->attach($user->id, ['is_owner' => true]);

        // Initialize Dealer Roles and Assign Owner Role
        $this->initializeDealerRoles($dealer, $user);

        return redirect()->route('admin.dealers.index')->with('success', 'Dealer created successfully.');
    }

    /**
     * Initialize default roles and permissions for a new dealer.
     */
    private function initializeDealerRoles(Dealer $dealer, User $owner): void
    {
        // Set permissions team context
        setPermissionsTeamId($dealer->id);

        $allPermissions = [
            'dealer.view_dashboard',
            'dealer.manage_inventory',
            'dealer.manage_leads',
            'dealer.view_staff',
            'dealer.create_staff',
            'dealer.edit_staff',
            'dealer.delete_staff',
            'dealer.manage_settings',
            'dealer.cancel_dealership',
        ];

        // Ensure permissions exist globally
        foreach ($allPermissions as $perm) {
            Permission::firstOrCreate([
                'name' => $perm,
                'guard_name' => 'web'
            ]);
        }

        // --- Dealer Owner Role ---
        $ownerRole = Role::firstOrCreate([
            'name' => 'dealer_owner',
            'guard_name' => 'web',
            'dealer_id' => $dealer->id,
        ], ['is_active' => true]);
        $ownerRole->syncPermissions($allPermissions);

        // Assign Role to Owner User
        $owner->assignRole($ownerRole);

        // --- Dealer Manager Role ---
        // Access everything except dealership cancellation
        $managerRole = Role::firstOrCreate([
            'name' => 'dealer_manager',
            'guard_name' => 'web',
            'dealer_id' => $dealer->id,
        ], ['is_active' => true]);
        $managerRole->syncPermissions(array_diff($allPermissions, ['dealer.cancel_dealership']));

        // --- Dealer Sales Role ---
        // Same as manager, but cannot delete staff
        $salesRole = Role::firstOrCreate([
            'name' => 'dealer_sales',
            'guard_name' => 'web',
            'dealer_id' => $dealer->id,
        ], ['is_active' => true]);
        $salesRole->syncPermissions(array_diff($allPermissions, ['dealer.cancel_dealership', 'dealer.delete_staff']));

        // --- Dealer Support Role ---
        // Role of the owner but cannot affect dealer account settings/cancellation. Expires in 4 days.
        $supportRole = Role::firstOrCreate([
            'name' => 'dealer_support',
            'guard_name' => 'web',
            'dealer_id' => $dealer->id,
        ], [
            'is_active' => true,
            'expires_at' => now()->addDays(4), // Setting 4 days as middle ground
        ]);
        $supportRole->syncPermissions(array_diff($allPermissions, ['dealer.manage_settings', 'dealer.cancel_dealership']));
    }

    public function edit(Dealer $dealer)
    {
        return view('admin.pages.dealers.edit', compact('dealer'));
    }

    public function update(Request $request, Dealer $dealer)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:50',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:16',
            'status' => 'required|string',
            'domains' => 'required|array|min:1',
            'domains.*' => 'required|string|unique:domains,domain,' . $dealer->id . ',dealer_id',
        ]);

        $dealer->update([
            'name' => $validated['company_name'],
            'company_name' => $validated['company_name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'status' => $validated['status'],
            'is_active' => $validated['status'] === DealerStatus::ACTIVE->value,
            'domain' => $validated['domains'][0],
        ]);

        // Sync domains
        $dealer->domains()->delete();
        foreach ($validated['domains'] as $index => $domainName) {
            $dealer->domains()->create([
                'domain' => $domainName,
                'is_primary' => $index === 0,
            ]);
        }

        return redirect()->route('admin.dealers.index')->with('success', 'Dealer updated successfully.');
    }

    public function toggleStatus(Dealer $dealer)
    {
        $dealer->status = $dealer->status === DealerStatus::ACTIVE ? DealerStatus::INACTIVE : DealerStatus::ACTIVE;
        $dealer->is_active = $dealer->status === DealerStatus::ACTIVE;
        $dealer->save();

        return back()->with('success', 'Dealer status updated.');
    }

    public function destroy(Dealer $dealer)
    {
        $dealer->delete();
        return redirect()->route('admin.dealers.index')->with('success', 'Dealer deleted.');
    }

    public function notify(Dealer $dealer)
    {
        $user = $dealer->owners()->first() ?? $dealer->users()->first();

        if (!$user) {
            return back()->with('error', 'No user associated with this dealer.');
        }

        if ($user->email_verified_at) {
            return back()->with('error', 'Dealer has already been verified.');
        }

        $token = \Illuminate\Support\Facades\Password::broker()->createToken($user);

        \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\DealerVerificationMail($token, $user->email));

        return back()->with('success', 'Verification notification sent to dealer.');
    }
}
