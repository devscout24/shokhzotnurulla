<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicle_specs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('vehicle_id')
                  ->constrained()
                  ->cascadeOnDelete()
                  ->cascadeOnUpdate();

            // Powertrain
            $table->string('aspiration', 100)->nullable();   // "N/A", "Turbocharged"
            $table->string('block_type', 10)->nullable();    // "I", "V", "H"
            $table->tinyInteger('cylinders')->unsigned()->nullable();
            $table->decimal('displacement', 6, 1)->nullable();  // e.g. 1.8, 5.7
            $table->string('power_cycle', 30)->nullable();   // "4-Stroke", "2-Stroke"
            $table->unsignedSmallInteger('max_horsepower')->nullable();
            $table->unsignedInteger('max_horsepower_at')->nullable();    // RPM
            $table->unsignedSmallInteger('max_torque')->nullable();
            $table->unsignedInteger('max_torque_at')->nullable();        // RPM

            // Transmission / Drivetrain (standardized text for syndication)
            // These are text fields (e.g. "Automatic", "FWD") used in feed exports
            // The FK columns (transmission_type_id, drivetrain_type_id) live on vehicles
            $table->string('transmission_standard', 50)->nullable();
            $table->string('drivetrain_standard', 20)->nullable();

            // Towing / Payload
            $table->unsignedInteger('towing_capacity')->nullable();
            $table->unsignedInteger('payload_capacity')->nullable();
            $table->unsignedInteger('gvwr')->nullable();
            $table->unsignedInteger('empty_weight')->nullable();
            $table->unsignedInteger('load_capacity')->nullable();

            // Fuel / Battery
            $table->decimal('fuel_tank', 5, 1)->nullable();
            $table->decimal('mpg_city', 4, 1)->nullable();
            $table->decimal('mpg_highway', 4, 1)->nullable();

            // EV specific
            $table->unsignedSmallInteger('ev_range')->nullable();           // miles
            $table->decimal('ev_battery_capacity', 5, 1)->nullable();      // kWh
            $table->decimal('ev_charger_rating', 4, 1)->nullable();        // kW

            // Dimensions
            $table->decimal('dimension_width', 6, 1)->nullable();
            $table->decimal('dimension_length', 6, 1)->nullable();
            $table->decimal('dimension_height', 6, 1)->nullable();
            $table->decimal('wheelbase', 6, 1)->nullable();
            $table->decimal('bed_length', 6, 1)->nullable();

            // Axle
            $table->string('axle', 30)->nullable();
            $table->decimal('axle_ratio', 4, 2)->nullable();

            // Rear door
            $table->string('rear_door_gate', 50)->nullable();

            // Wheels / Tires
            $table->string('front_wheel', 30)->nullable();
            $table->string('rear_wheel', 30)->nullable();
            $table->string('front_tire', 30)->nullable();
            $table->string('rear_tire', 30)->nullable();

            $table->unique('vehicle_id');
            $table->timestamps();
            $table->softDeletes('deleted_at', precision: 0);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicle_specs');
    }
};