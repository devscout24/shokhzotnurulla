<?php
namespace Database\Seeders;

use App\Models\Dealership\Dealer;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DealerRoleSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $dealer = Dealer::where('slug', 'dealer-1')->firstOrFail();

        // ⬅️ Teams ke liye zaroori
        setPermissionsTeamId($dealer->id);

        // --- Dealer permissions ---
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

        foreach ($allPermissions as $perm) {
            Permission::firstOrCreate([
                'name'       => $perm,
                'guard_name' => 'web',
            ]);
        }

        // --- Dealer Owner Role ---
        $ownerRole = Role::firstOrCreate(
            [
                'name'       => 'dealer_owner',
                'guard_name' => 'web',
                'dealer_id'  => $dealer->id,
            ],
            ['is_active' => true]
        );

        $ownerRole->syncPermissions($allPermissions);

        // Dealer Owner User assign
        $dealerOwner = $dealer->users()
            ->wherePivot('is_owner', true)
            ->firstOrFail();

        $dealerOwner->assignRole($ownerRole);

        // --- Staff Roles ---

        // 1. Manager
        $managerRole = Role::firstOrCreate([
            'name'       => 'dealer_manager',
            'guard_name' => 'web',
            'dealer_id'  => $dealer->id,
        ], ['is_active' => true]);
        $managerRole->syncPermissions(array_diff($allPermissions, ['dealer.cancel_dealership']));

        // 2. Sales
        $salesRole = Role::firstOrCreate([
            'name'       => 'dealer_sales',
            'guard_name' => 'web',
            'dealer_id'  => $dealer->id,
        ], ['is_active' => true]);
        $salesRole->syncPermissions(array_diff($allPermissions, ['dealer.cancel_dealership', 'dealer.delete_staff']));

        // 3. Support (Expires in 4 days)
        $supportRole = Role::firstOrCreate([
            'name'       => 'dealer_support',
            'guard_name' => 'web',
            'dealer_id'  => $dealer->id,
        ], [
            'is_active'  => true,
            'expires_at' => now()->addDays(4),
        ]);
        $supportRole->syncPermissions(array_diff($allPermissions, ['dealer.manage_settings', 'dealer.cancel_dealership']));

        // --- Assign roles to 3 staff users ---
        $staffUsers = $dealer->users()
            ->wherePivot('is_owner', false)
            ->get();

        $roles = [$managerRole, $salesRole, $supportRole];

        foreach ($staffUsers as $index => $user) {
            if (isset($roles[$index])) {
                $user->assignRole($roles[$index]);
            }
        }
    }
}
