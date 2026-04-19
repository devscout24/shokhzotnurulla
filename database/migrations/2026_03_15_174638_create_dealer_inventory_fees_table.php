<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dealer_inventory_fees', function (Blueprint $table) {
            $table->id();

            $table->foreignId('dealer_id')
                  ->constrained('dealers')
                  ->cascadeOnDelete();

            $table->string('name', 150);
            $table->string('description', 500)->nullable();

            $table->enum('type', ['amount', 'percentage']);

            $table->decimal('value', 10, 2)
                  ->comment('Flat dollar amount or percentage value');

            $table->enum('tax', ['pre-tax', 'post-tax'])->default('post-tax');

            $table->boolean('is_optional')->default(false)
                  ->comment('false = Guaranteed, true = Optional');

            $table->enum('condition', ['any', 'new', 'used', 'cpo', 'vpo'])
                  ->default('any');

            $table->unsignedSmallInteger('sort_order')->default(0);

            $table->timestamps();

            // ── Indexes ──────────────────────────────────────────────
            $table->index(['dealer_id', 'sort_order'], 'dif_dealer_sort');
            $table->index(['dealer_id', 'condition'],  'dif_dealer_condition');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dealer_inventory_fees');
    }
};