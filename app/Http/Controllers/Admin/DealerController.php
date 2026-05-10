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

        $permissions = [
            'dealer.view_dashboard',
            'dealer.manage_inventory',
            'dealer.manage_staff',
            'dealer.manage_leads',
            'dealer.manage_settings',
        ];

        // Ensure permissions exist globally
        foreach ($permissions as $perm) {
            Permission::firstOrCreate([
                'name' => $perm,
                'guard_name' => 'web'
            ]);
        }

        // Create Owner Role for this dealer
        $ownerRole = Role::firstOrCreate([
            'name' => 'dealer_owner',
            'guard_name' => 'web',
            'dealer_id' => $dealer->id,
        ], [
            'is_active' => true,
        ]);

        $ownerRole->syncPermissions($permissions);

        // Assign Role to User
        $owner->assignRole($ownerRole);

        // Create other standard roles for the dealer
        $staffRoleNames = ['View only', 'Content editor', 'Billing', 'Administrator'];
        foreach ($staffRoleNames as $name) {
            Role::firstOrCreate([
                'name' => $name,
                'guard_name' => 'web',
                'dealer_id' => $dealer->id,
            ], [
                'is_active' => true,
            ]);
        }
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
