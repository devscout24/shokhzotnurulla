<?php
namespace App\Http\Controllers\Admin;

use App\Enums\DealerStatus;
use App\Helpers\TimeHelper;
use App\Http\Controllers\Controller;
use App\Mail\DealerVerificationMail;
use App\Models\Dealership\Dealer;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Services\Menu\MenuService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class DealerController extends Controller
{
    public function index()
    {
        $dealers = Dealer::query()->latest()->paginate(15);

        return view('admin.pages.dealers.index', compact('dealers'));
    }

    public function search(Request $request)
    {
        $q = trim($request->query('q', ''));

        $dealers = Dealer::query()
            ->where(function ($query) use ($q) {
                $query->where('internal_id', 'like', "%{$q}%")
                      ->orWhere('company_name', 'like', "%{$q}%")
                      ->orWhere('domain', 'like', "%{$q}%")
                      ->orWhere('email', 'like', "%{$q}%");
            })
            ->latest()
            ->limit(50)
            ->get(['id', 'internal_id', 'company_name', 'slug', 'domain', 'email', 'status']);

        return response()->json($dealers);
    }

    public function create()
    {
        return view('admin.pages.dealers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:50',
            'email'        => 'required|email|unique:users,email',
            'phone'        => 'nullable|string|max:16',
            'domains'      => 'required|array|min:1',
            'domains.*'    => 'required|string|unique:domains,domain',
        ]);

        DB::transaction(function () use ($validated, $request) {
            $dealerData = [
                'name'         => $validated['company_name'],
                'company_name' => $validated['company_name'],
                'slug'         => Str::slug($validated['company_name']),
                'email'        => $validated['email'],
                'phone'        => $validated['phone'],
                'status'       => DealerStatus::INACTIVE,
                'is_active'    => false,
                'domain'       => $validated['domains'][0], // Set the first one as the primary on dealer table too
                'internal_id'  => TimeHelper::generateNumericId(),
            ];

            $dealer = Dealer::create($dealerData);

            if(!$dealer) {
                throw new \Exception('Failed to create dealer');
            }
            if (!$dealer->internal_id) {
                throw new \Exception('Dealer ID or internal ID not found after creation');
            }

            foreach ($validated['domains'] as $index => $domainName) {
                $dealer->domains()->create([
                    'domain'     => $domainName,
                    'is_primary' => $index === 0,
                ]);
            }

            if ($request->has('locations')) {
                $locOrder = 0;
                foreach ($request->input('locations') as $index => $locData) {
                    if (empty($locData['name'])) {
                        continue;
                    }

                    $dealer->locations()->create([
                        'name'       => $locData['name'],
                        'street1'    => $locData['street1'] ?? '',
                        'city'       => $locData['city'] ?? '',
                        'state'      => $locData['state'] ?? '',
                        'postalcode' => $locData['postalcode'] ?? '',
                        'country'    => 'US',
                        'order'      => $locOrder++,
                    ]);
                }
            }

            $user = User::create([
                'first_name'        => $validated['company_name'],
                'last_name'         => 'Admin',
                'email'             => $validated['email'],
                'password'          => Hash::make(Str::random(16)),
                'is_system_user'    => false,
                'current_dealer_id' => $dealer->id,
            ]);

            $dealer->owners()->attach($user->id, ['is_owner' => true]);

            // Initialize Dealer Roles and Assign Owner Role
            $this->initializeDealerRoles($dealer, $user);

            (new MenuService)->buildForDealer($dealer);
        });

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
                'name'       => $perm,
                'guard_name' => 'web',
            ]);
        }

        // --- Dealer Owner Role ---
        $ownerRole = Role::firstOrCreate([
            'name'       => 'dealer_owner',
            'guard_name' => 'web',
            'dealer_id'  => $dealer->id,
        ], ['is_active' => true]);
        $ownerRole->syncPermissions($allPermissions);

        // Assign Role to Owner User
        $owner->assignRole($ownerRole);

        // --- Dealer Manager Role ---
        // Access everything except dealership cancellation
        $managerRole = Role::firstOrCreate([
            'name'       => 'dealer_manager',
            'guard_name' => 'web',
            'dealer_id'  => $dealer->id,
        ], ['is_active' => true]);
        $managerRole->syncPermissions(array_diff($allPermissions, ['dealer.cancel_dealership']));

        // --- Dealer Sales Role ---
        // Same as manager, but cannot delete staff
        $salesRole = Role::firstOrCreate([
            'name'       => 'dealer_sales',
            'guard_name' => 'web',
            'dealer_id'  => $dealer->id,
        ], ['is_active' => true]);
        $salesRole->syncPermissions(array_diff($allPermissions, ['dealer.cancel_dealership', 'dealer.delete_staff']));

        // --- Dealer Support Role ---
        // Role of the owner but cannot affect dealer account settings/cancellation. Expires in 4 days.
        $supportRole = Role::firstOrCreate([
            'name'       => 'dealer_support',
            'guard_name' => 'web',
            'dealer_id'  => $dealer->id,
        ], [
            'is_active'  => true,
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
            'email'        => 'nullable|email',
            'phone'        => 'nullable|string|max:16',
            'status'       => 'required|string',
            'domains'      => 'required|array|min:1',
            'domains.*'    => 'required|string|unique:domains,domain,' . $dealer->id . ',dealer_id',
        ]);

        DB::transaction(function () use ($validated, $request, $dealer) {
            $dealer->update([
                'name'         => $validated['company_name'],
                'company_name' => $validated['company_name'],
                'email'        => $validated['email'],
                'phone'        => $validated['phone'],
                'status'       => $validated['status'],
                'is_active'    => $validated['status'] === DealerStatus::ACTIVE->value,
                'domain'       => $validated['domains'][0],
            ]);

            // Sync domains
            $dealer->domains()->delete();
            foreach ($validated['domains'] as $index => $domainName) {
                $dealer->domains()->create([
                    'domain'     => $domainName,
                    'is_primary' => $index === 0,
                ]);
            }

            // Sync locations
            $submittedLocationIds = [];
            if ($request->has('locations')) {
                $locOrder = 0;
                foreach ($request->input('locations') as $index => $locData) {
                    if (empty($locData['name'])) {
                        continue;
                    }

                    $locPayload = [
                        'name'       => $locData['name'],
                        'street1'    => $locData['street1'] ?? '',
                        'city'       => $locData['city'] ?? '',
                        'state'      => $locData['state'] ?? '',
                        'postalcode' => $locData['postalcode'] ?? '',
                        'country'    => 'US',
                        'order'      => $locOrder++,
                    ];

                    if (! empty($locData['id'])) {
                        $location = $dealer->locations()->find($locData['id']);
                        if ($location) {
                            $location->update($locPayload);
                            $submittedLocationIds[] = $location->id;
                        }
                    } else {
                        $location               = $dealer->locations()->create($locPayload);
                        $submittedLocationIds[] = $location->id;
                    }
                }
            }
            $dealer->locations()->whereNotIn('id', $submittedLocationIds)->delete();
        });

        return redirect()->route('admin.dealers.index')->with('success', 'Dealer updated successfully.');
    }

    public function toggleStatus(Dealer $dealer)
    {
        $dealer->status    = $dealer->status === DealerStatus::ACTIVE ? DealerStatus::INACTIVE : DealerStatus::ACTIVE;
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

        if (! $user) {
            return back()->with('error', 'No user associated with this dealer.');
        }

        if ($user->email_verified_at) {
            return back()->with('error', 'Dealer has already been verified.');
        }

        $token = Password::broker()->createToken($user);

        Mail::to($user->email)->send(new DealerVerificationMail($token, $user->email));

        return back()->with('success', 'Verification notification sent to dealer.');
    }
}
