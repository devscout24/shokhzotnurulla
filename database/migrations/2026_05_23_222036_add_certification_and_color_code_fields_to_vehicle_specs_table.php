<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vehicle_specs', function (Blueprint $table) {
            // Certification flags
            $table->boolean('factory_certified')->nullable()->after('rear_tire');
            $table->boolean('dealer_certified')->nullable()->after('factory_certified');

            // Chrome & color codes
            $table->string('chrome_style_id', 50)->nullable()->after('dealer_certified');
            $table->string('exterior_color_code', 50)->nullable()->after('chrome_style_id');
            $table->string('interior_color_code', 50)->nullable()->after('exterior_color_code');
            $table->string('interior_material', 100)->nullable()->after('interior_color_code');
        });
    }

    public function down(): void
    {
        Schema::table('vehicle_specs', function (Blueprint $table) {
            $table->dropColumn([
                'factory_certified',
                'dealer_certified',
                'chrome_style_id',
                'exterior_color_code',
                'interior_color_code',
                'interior_material',
            ]);
        });
    }
};
