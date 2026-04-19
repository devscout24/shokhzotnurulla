<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicle_prices', function (Blueprint $table) {
            $table->id();

            $table->foreignId('vehicle_id')
                  ->constrained()
                  ->cascadeOnDelete()
                  ->cascadeOnUpdate();

            // Core pricing
            $table->decimal('msrp', 10, 2)->nullable();
            $table->decimal('dealer_cost', 10, 2)->nullable();

            // Pricing disclaimer (rich text / HTML)
            $table->text('pricing_disclaimer')->nullable();

            // Add-ons & special discounts
            $table->decimal('special_price', 10, 2)->nullable();
            $table->string('special_price_label', 100)->nullable();
            $table->decimal('addon_price', 10, 2)->nullable();
            $table->string('addon_price_label', 100)->nullable();
            $table->text('addon_price_description')->nullable();

            // Adjustment label (shown when list_price > msrp)
            $table->string('adjustment_label', 100)->nullable();

            // Third-party / syndication pricing
            $table->decimal('internet_price', 10, 2)->nullable();
            $table->decimal('asking_price', 10, 2)->nullable();

            // Sold information
            $table->decimal('sold_price', 10, 2)->nullable();
            $table->date('sold_date')->nullable();
            $table->string('sold_to', 200)->nullable(); // customer name (free text for now)

            $table->unique('vehicle_id');
            $table->timestamps();
            $table->softDeletes('deleted_at', precision: 0);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicle_prices');
    }
};