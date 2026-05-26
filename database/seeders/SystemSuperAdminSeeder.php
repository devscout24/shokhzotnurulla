<?php

namespace Database\Seeders;

use App\Helpers\TimeHelper;
use App\Models\Dealership\Dealer;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SystemSuperAdminSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // System dealer (team)
        $systemDealer = Dealer::firstOrCreate(
            ['slug' => config('systemuser.dealer_slug')],
            [
                'company_name' => 'dev comp 1',
                'name' => 'System Dealer',
                'email' => 'superadmin@gmail.com',
                'phone' => '+923280287524',
                'is_active' => true,
                'internal_id' => TimeHelper::generateNumericId(),
            ]
        );

        // Super Admin User
        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@gmail.com'],
            [
                'first_name' => 'Super',
                'last_name' => 'Admin',
                'email_verified_at' => now(),
                'password' => Hash::make('12345678'), // change in prod
                'is_active' => true,
                'is_system_user' => true,
                'current_dealer_id' => $systemDealer->id,
            ]
        );

        // Attach to system dealer pivot
        $superAdmin->dealers()->syncWithoutDetaching([
            $systemDealer->id => ['is_owner' => true],
        ]);

        $systemDealer->domains()->create([
            'domain' => 'shokh.test',
            'is_primary' => true,
            'is_verified' => true,
        ]);

        // 3 System Staff Users
        for ($i = 1; $i <= 3; $i++) {
            $staff = User::firstOrCreate(
                ['email' => "systemstaff{$i}@gmail.com"],
                [
                    'first_name' => 'System',
                    'last_name' => "Staff {$i}",
                    'email_verified_at' => now(),
                    'password' => Hash::make('12345678'), // change in prod
                    'is_active' => true,
                    'is_system_user' => true,
                    'current_dealer_id' => $systemDealer->id,
                ]
            );

            // Attach staff to system dealer pivot (is_owner = false)
            $staff->dealers()->syncWithoutDetaching([
                $systemDealer->id => ['is_owner' => false],
            ]);
        }
    }
}
