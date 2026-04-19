<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        $this->call([
            SystemSuperAdminSeeder::class,
            SuperAdminRoleSeeder::class,
            DefaultDealerUserSeeder::class,
            DealerRoleSeeder::class,

            // Catalog — order matters (parents before children)
            MakeSeeder::class,
            MakeModelSeeder::class,        // depends on makes

            BodyTypeGroupSeeder::class,
            BodyTypeSeeder::class,         // depends on body_type_groups

            BodyStyleGroupSeeder::class,
            BodyStyleSeeder::class,

            ColorSeeder::class,
            FuelTypeSeeder::class,
            TransmissionTypeSeeder::class,
            DrivetrainTypeSeeder::class,

            FactoryOptionCategorySeeder::class,
            FactoryOptionSeeder::class,
        ]);
    }
}
