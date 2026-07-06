<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('vehile_vin_data', function (Blueprint $table) {
            $table->id();
            $table->string('vin')->unique();
            $table->json('default')->nullable();
            $table->json('vehile_databases')->nullable();
            $table->json('data_one')->nullable();
            $table->json('custom')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehile_vin_data');
    }
};
