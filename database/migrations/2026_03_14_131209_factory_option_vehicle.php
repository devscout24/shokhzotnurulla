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
        Schema::create('factory_option_vehicle', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')
                  ->constrained()
                  ->cascadeOnDelete()
                  ->cascadeOnUpdate();
            $table->foreignId('factory_option_id')
                  ->constrained('factory_options')
                  ->cascadeOnDelete()
                  ->cascadeOnUpdate();
            $table->boolean('is_starred')->default(false);
            $table->unique(['vehicle_id', 'factory_option_id']);
            $table->index(['vehicle_id', 'is_starred']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('factory_option_vehicle');
    }
};
