<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Dealership\Dealer;
use Illuminate\Support\Facades\Hash;

class DefaultDealerUserSeeder extends Seeder
{
    use WithoutModelEvents;
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $dealer = Dealer::firstOrCreate(
            ['slug' => 'dealer-1'],
            [
                'name' => 'Dealer 1',
                'email' => 'irfanulhaq6312@gmail.com',
                'phone' => '+923280287525',
                'is_active' => true
            ]
        );

        // Dealer Owner User
        $dealerOwner = User::firstOrCreate(
            ['email' => 'irfanulhaq6312@gmail.com'],
            [
                'first_name' => 'Dealer',
                'last_name' => 'Owner',
                'email_verified_at' => now(),
                'password' => Hash::make('Ir_f2026UKsa$deal'), // change in prod
                'is_active' => true,
                'is_system_user' => false,
                'current_dealer_id' => $dealer->id,
            ]
        );

        // Attach to dealer pivot
        $dealerOwner->dealers()->syncWithoutDetaching([
            $dealer->id => ['is_owner' => true],
        ]);

        // --- 3 Dealer Staff Users ---
        for ($i = 1; $i <= 3; $i++) {
            $staff = User::firstOrCreate(
                ['email' => "dealer1staff{$i}@gmail.com"],
                [
                    'first_name' => "Dealer",
                    'last_name' => "Staff {$i}",
                    'email_verified_at' => now(),
                    'password' => Hash::make('12345678'), // change in prod
                    'is_active' => true,
                    'is_system_user' => false,
                    'current_dealer_id' => $dealer->id,
                ]
            );

            // Attach staff to dealer pivot (is_owner = false)
            $staff->dealers()->syncWithoutDetaching([
                $dealer->id => ['is_owner' => false],
            ]);
        }
    }
}
