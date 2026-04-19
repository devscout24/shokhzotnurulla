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
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();

            $table->foreignId('dealer_id')
                  ->constrained()
                  ->cascadeOnDelete()
                  ->cascadeOnUpdate();

            $table->char('ulid', 26);

            $table->string('stock_number', 50);
            $table->string('vin', 17)->nullable();
            $table->string('model_number', 50)->nullable();
            $table->unsignedSmallInteger('year');

            $table->foreignId('make_id')
                  ->constrained()
                  ->restrictOnDelete()
                  ->cascadeOnUpdate();

            $table->foreignId('make_model_id')
                  ->constrained()
                  ->restrictOnDelete()
                  ->cascadeOnUpdate();

            $table->string('trim', 100)->nullable();

            $table->foreignId('body_type_id')
                  ->constrained()
                  ->restrictOnDelete()
                  ->cascadeOnUpdate();

            $table->foreignId('body_style_id')
                  ->nullable()
                  ->constrained()
                  ->nullOnDelete()
                  ->cascadeOnUpdate();

            $table->string('vehicle_condition', 20);
            $table->boolean('is_certified')->default(false);
            $table->boolean('is_commercial')->default(false);
            $table->string('location_status', 20)->default('lot');

            $table->foreignId('fuel_type_id')
                  ->nullable()
                  ->constrained()
                  ->nullOnDelete()
                  ->cascadeOnUpdate();

            $table->foreignId('transmission_type_id')
                  ->nullable()
                  ->constrained()
                  ->nullOnDelete()
                  ->cascadeOnUpdate();

            $table->foreignId('drivetrain_type_id')
                  ->nullable()
                  ->constrained()
                  ->nullOnDelete()
                  ->cascadeOnUpdate();

            $table->string('engine', 150)->nullable();
            $table->unsignedInteger('mileage')->nullable();

            $table->foreignId('exterior_color_id')
                  ->nullable()
                  ->constrained('colors')
                  ->nullOnDelete()
                  ->cascadeOnUpdate();

            $table->foreignId('interior_color_id')
                  ->nullable()
                  ->constrained('colors')
                  ->nullOnDelete()
                  ->cascadeOnUpdate();

            $table->tinyInteger('doors')->unsigned()->nullable();
            $table->tinyInteger('seating_capacity')->unsigned()->nullable();

            $table->decimal('list_price', 10, 2)->nullable();
            $table->unsignedInteger('original_price')->nullable();
            $table->date('inventory_date')->nullable();

            $table->boolean('featured')->default(false);
            $table->enum('status', ['draft', 'active', 'sold'])->default('draft');
            $table->boolean('is_on_hold')->default(false);
            $table->boolean('is_spotlight')->default(false);
            $table->boolean('ignore_feed_updates')->default(false);
            $table->enum('source', ['feed', 'manual'])->default('manual');

            $table->unsignedInteger('total_views')->default(0);
            $table->unsignedInteger('total_leads')->default(0);
            $table->unsignedSmallInteger('days_on_lot')->default(0);

            $table->timestamp('listed_at')->nullable();
            $table->timestamp('sold_at')->nullable();


            // Unique constraints
            $table->unique(['dealer_id', 'stock_number']);
            $table->unique(['dealer_id', 'vin']);
            $table->unique('ulid');

            // Indexes
            $table->index('status');
            $table->index('year');
            $table->index('mileage');
            $table->index('list_price');
            $table->index('vehicle_condition');
            $table->index('listed_at');

            // Composite indexes
            $table->index(['dealer_id', 'status']);
            $table->index(['dealer_id', 'status', 'year']);
            $table->index(['dealer_id', 'status', 'body_type_id']);
            $table->index(['dealer_id', 'status', 'make_id']);
            $table->index(['dealer_id', 'status', 'list_price']);
            $table->index(['dealer_id', 'status', 'mileage']);

            $table->timestamps();
            $table->softDeletes('deleted_at', precision: 0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
