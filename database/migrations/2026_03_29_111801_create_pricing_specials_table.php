<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pricing_specials', function (Blueprint $table) {
            $table->id();

            $table->foreignId('dealer_id')
                  ->constrained()
                  ->cascadeOnDelete()
                  ->cascadeOnUpdate();

            // ── Basic ──────────────────────────────────────────────────────────
            $table->string('title', 150);
            $table->enum('type', ['formfill', 'override'])->nullable();
            $table->string('button_text', 100)->nullable();     // formfill only
            $table->string('discount_label', 100)->nullable();  // override only
            $table->boolean('stackable')->default(false);       // override only
            $table->unsignedSmallInteger('priority')->default(0);

            // ── Discount ───────────────────────────────────────────────────────
            $table->enum('discount_type', [
                'fixed', 'percentage', 'dollars',
                'offsetdollar', 'special', 'offsetincrease', 'increase',
            ])->nullable();
            $table->decimal('amount', 10, 2)->nullable();
            $table->decimal('finance_rate', 5, 2)->nullable();       // special only
            $table->unsignedSmallInteger('finance_term')->nullable(); // special only, months

            // ── Vehicle Criteria ───────────────────────────────────────────────
            $table->string('condition', 50)->nullable();
            $table->boolean('is_certified')->nullable();
            $table->string('model_number', 100)->nullable();
            $table->unsignedSmallInteger('year')->nullable();

            $table->foreignId('make_id')
                  ->nullable()
                  ->constrained()
                  ->nullOnDelete()
                  ->cascadeOnUpdate();

            $table->foreignId('make_model_id')
                  ->nullable()
                  ->constrained()
                  ->nullOnDelete()
                  ->cascadeOnUpdate();

            $table->string('trim', 100)->nullable();
            $table->string('body_style', 100)->nullable();

            $table->foreignId('exterior_color_id')
                  ->nullable()
                  ->constrained('colors')
                  ->nullOnDelete()
                  ->cascadeOnUpdate();

            $table->string('stock_number', 50)->nullable();
            $table->string('tag', 100)->nullable();
            $table->unsignedInteger('min_days')->nullable();
            $table->unsignedInteger('max_days')->nullable();

            // ── Schedule & Disclaimer ──────────────────────────────────────────
            $table->boolean('send_email')->default(false);
            $table->boolean('hide_price')->default(false);
            $table->date('starts_at')->nullable();
            $table->date('ends_at')->nullable();
            $table->text('notes')->nullable();
            $table->text('disclaimer')->nullable();

            $table->boolean('is_enabled')->default(true);

            $table->timestamps();
            $table->softDeletes('deleted_at', precision: 0);

            $table->index(['dealer_id', 'is_enabled']);
            $table->index(['dealer_id', 'ends_at']);
            $table->index(['dealer_id', 'discount_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pricing_specials');
    }
};