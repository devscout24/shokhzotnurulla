<?php

use Database\Seeders\FactoryOptionCategorySeeder;
use Database\Seeders\FactoryOptionSeeder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        (new FactoryOptionCategorySeeder)->run();
        (new FactoryOptionSeeder)->run();

        $categoryId = DB::table('factory_option_categories')
            ->where('name', 'Safety and Security')
            ->value('id');

        if (! $categoryId) {
            return;
        }

        // Remove options no longer in the canonical Safety & Security catalog.
        DB::table('factory_options')
            ->where('category_id', $categoryId)
            ->whereIn('option_key', [
                'kneeairbags_dualfront',
                'rearbraketype_drum',
                'camerasystem_rearview',
                'activeheadrestraints_dualfront',
                'antitheft_glassbreakage',
            ])
            ->delete();
    }

    public function down(): void
    {
        // Data sync migration; no rollback.
    }
};
