<?php
namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;
use App\Models\User;
use App\Models\Dealership\Dealer;

class DealerRoleSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $dealer = Dealer::where('slug', 'dealer-1')->firstOrFail();

        // ⬅️ Teams ke liye zaroori
        setPermissionsTeamId($dealer->id);

        // --- Dealer permissions ---
        $permissions = [
            'dealer.view_dashboard',
            'dealer.manage_inventory',
            'dealer.manage_staff',
            'dealer.manage_leads',
            'dealer.manage_settings',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate([
                'name' => $perm,
                'guard_name' => 'web'
            ]);
        }

        // --- Dealer Owner Role ---
        $ownerRole = Role::firstOrCreate(
            [
                'name' => 'dealer_owner',
                'guard_name' => 'web',
                'dealer_id' => $dealer->id,
            ],
            [
                'is_active' => true,
            ]
        );

        $ownerRole->syncPermissions($permissions);

        // Dealer Owner User assign
        $dealerOwner = $dealer->users()
            ->wherePivot('is_owner', true)
            ->firstOrFail();

        $dealerOwner->assignRole($ownerRole);

        // --- Staff Roles ---
        $staffRoleNames = ['dealer_manager', 'dealer_sales', 'dealer_support'];
        $staffRoles = [];

        foreach ($staffRoleNames as $name) {
            $staffRoles[$name] = Role::firstOrCreate(
                [
                    'name' => $name,
                    'guard_name' => 'web',
                    'dealer_id' => $dealer->id,
                ],
                ['is_active' => true]
            );
        }

        // --- Assign roles to 3 staff users ---
        $staffUsers = $dealer->users()
            ->wherePivot('is_owner', false)
            ->get();

        foreach ($staffUsers as $index => $user) {
            $roleName = $staffRoleNames[$index] ?? 'dealer_sales'; // fallback
            $user->assignRole($staffRoles[$roleName]);
        }
    }
}