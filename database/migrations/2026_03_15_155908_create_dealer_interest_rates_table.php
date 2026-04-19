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
        Schema::create('dealer_interest_rates', function (Blueprint $table) {
            $table->id();

            $table->foreignId('dealer_id')
                  ->constrained('dealers')
                  ->cascadeOnUpdate()
                  ->cascadeOnDelete();

            $table->string('make', 100)->nullable()
                  ->comment('null = Any make');

            $table->unsignedSmallInteger('min_model_year')
                  ->comment('Oldest year in range, e.g. 2020');
            $table->unsignedSmallInteger('max_model_year')
                  ->comment('Newest year in range, e.g. 2026');

            $table->unsignedSmallInteger('min_term')->default(0)
                  ->comment('Minimum loan term in months');
            $table->unsignedSmallInteger('max_term')->default(36)
                  ->comment('Maximum loan term in months');

            $table->unsignedSmallInteger('min_credit_score')->nullable()
                  ->comment('FICO 300–850');
            $table->unsignedSmallInteger('max_credit_score')->nullable()
                  ->comment('FICO 300–850');

            $table->enum('condition', ['any', 'new', 'used', 'cpo', 'vpo'])
                  ->default('any');

            $table->decimal('rate', 5, 2)
                  ->comment('Interest rate percentage, e.g. 5.60');

            $table->unsignedSmallInteger('sort_order')->default(0);

            // ── Indexes ──────────────────────────────────────────────
            $table->index(['dealer_id', 'condition'],
                'dir_dealer_condition');

            $table->index(['dealer_id', 'min_model_year', 'max_model_year'],
                'dir_dealer_year_range');

            $table->index(
                ['dealer_id', 'condition', 'min_credit_score', 'max_credit_score'],
                'dir_dealer_rate_lookup'
            );

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dealer_interest_rates');
    }
};
