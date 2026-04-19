<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicle_printables', function (Blueprint $table) {
            $table->id();

            $table->foreignId('vehicle_id')
                  ->constrained()
                  ->cascadeOnDelete()
                  ->cascadeOnUpdate();

            $table->foreignId('dealer_id')
                  ->constrained()
                  ->cascadeOnDelete()
                  ->cascadeOnUpdate();

            // Window Sticker | Buyer's Guide | Generate Quote
            $table->string('name', 100);

            // Print Sticker | Print Guide | Generate Quote
            $table->string('cta', 100)->nullable();

            $table->string('description', 255)->nullable();

            // portrait | landscape
            $table->string('layout', 20)->default('portrait');

            // Custom HTML template — null = use system default
            $table->longText('html_template')->nullable();

            $table->timestamps();

            // Only one of each printable type per vehicle
            $table->unique(['vehicle_id', 'name']);
            $table->index(['vehicle_id', 'dealer_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicle_printables');
    }
};
