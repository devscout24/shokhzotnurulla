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
        Schema::create('digital_retail_settings', function (Blueprint $table) {
            $table->id();

            $table->foreignId('dealer_id')
                ->constrained()
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->unsignedSmallInteger('shipping_free_miles')->default(250);
            $table->decimal('shipping_discount_dollars', 8, 2)->default(199.00);
            $table->decimal('deposit_minimum', 8, 2)->default(500.00);
            $table->unsignedSmallInteger('deposit_hold_hours')->default(48);
            $table->unsignedSmallInteger('digital_retail_hold_hours')->default(72);
            $table->unsignedSmallInteger('trade_days_valid')->default(14);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('digital_retail_settings');
    }
};
