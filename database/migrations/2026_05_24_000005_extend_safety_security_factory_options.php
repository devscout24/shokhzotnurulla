<?php

use Database\Seeders\FactoryOptionCategorySeeder;
use Database\Seeders\FactoryOptionSeeder;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        (new FactoryOptionCategorySeeder)->run();
        (new FactoryOptionSeeder)->run();
    }

    public function down(): void
    {
        // Catalog extensions are additive; no rollback.
    }
};
