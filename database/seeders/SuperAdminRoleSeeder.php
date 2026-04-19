<?php
namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Role;
use App\Models\Permission;
use App\Models\User;
use App\Models\Dealership\Dealer;

class SuperAdminRoleSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $systemDealer = Dealer::where('slug', config('systemuser.dealer_slug'))->firstOrFail();

        // Zaroori hai Teams ke saath
        setPermissionsTeamId($systemDealer->id);

        // --- Permissions
        $permissions = [
            'system.view_dashboard',
            'system.manage_dealers',
            'system.manage_users',
            'system.manage_roles',
            'system.manage_permissions',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate([
                'name' => $perm,
                'guard_name' => 'web'
            ]);
        }

        // --- Super Admin Role ---
        $superAdminRole = Role::firstOrCreate(
            [
                'name'      => 'super_admin',
                'guard_name'=> 'web',
                'dealer_id' => $systemDealer->id,
            ],
            [
                'is_active' => true,
            ]
        );

        // Super Admin User assign
        $superAdmin = $systemDealer->users()
            ->wherePivot('is_owner', true)
            ->firstOrFail();

        $superAdmin->assignRole($superAdminRole);

        // --- New Staff Roles ---
        $staffRoleNames = ['system_manager', 'system_support', 'system_operations'];

        $staffRoles = [];
        foreach ($staffRoleNames as $name) {
            $staffRoles[$name] = Role::firstOrCreate(
                [
                    'name' => $name,
                    'guard_name' => 'web',
                    'dealer_id' => $systemDealer->id,
                ],
                ['is_active' => true]
            );
        }

        // --- 3 System Staff Users assign roles ---
        $staffUsers = $systemDealer->users()
            ->wherePivot('is_owner', false)
            ->get();

        foreach ($staffUsers as $index => $user) {
            $roleName = $staffRoleNames[$index] ?? 'system_operations'; // safety fallback
            $user->assignRole($staffRoles[$roleName]);
        }
    }
}