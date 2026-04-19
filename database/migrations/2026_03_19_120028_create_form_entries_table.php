<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('form_entries', function (Blueprint $table) {

            $table->id();

            // ── Dealer scope ──────────────────────────────────────────────
            $table->foreignId('dealer_id')
                  ->constrained('dealers')
                  ->cascadeOnDelete();

            // ── Form type ─────────────────────────────────────────────────
            $table->enum('form_type', [
                'trade_in',
                'get_approved',
                'unlock_calculator',
                'managers_special',
                'ask_question',
                'schedule_test_drive',
                'contact_us',
                'unlock_eprice',
            ]);

            // ── Get Approved specific ─────────────────────────────────────
            $table->enum('borrower_type', ['single', 'joint'])->nullable();

            // ── Status ────────────────────────────────────────────────────
            $table->enum('status', ['complete', 'abandoned'])->default('abandoned');

            // ── Read / Unread ─────────────────────────────────────────────
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();

            // ── Vehicle reference ─────────────────────────────────────────
            $table->foreignId('vehicle_id')
                  ->nullable()
                  ->constrained('vehicles')
                  ->nullOnDelete();

            // ── Referrer URL ──────────────────────────────────────────────
            $table->string('referrer', 1000)->nullable();

            // ── Common contact fields (extracted for search/filter) ───────
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->string('email', 255);
            $table->string('phone', 20)->nullable();

            // ── Form-specific data ────────────────────────────────────────
            $table->json('data')->nullable();

            // ── NPS Rating ────────────────────────────────────────────────
            $table->unsignedTinyInteger('nps_rating')->nullable(); // 1-5

            // ── Visitor / Device / UTM tracking (future use) ─────────────
            // Stored as JSON — flexible for future fields
            // Structure:
            // {
            //   "ip": "68.84.80.13",
            //   "location": { "city": "Franklin", "state": "TN", "country": "US" },
            //   "device": { "type": "Smartphone", "brand": "Apple", "model": "iPhone" },
            //   "browser": { "client": "Mobile Safari", "os": "iOS", "os_version": "26.3.1" },
            //   "traffic": {
            //     "referer": "google.com",
            //     "classification": "organic search",
            //     "utm_source": null,
            //     "utm_campaign": null,
            //     "utm_term": null,
            //     "utm_content": null
            //   },
            //   "visit_duration_seconds": 108
            // }
            $table->json('visitor_data')->nullable();

            // ── Timestamps ────────────────────────────────────────────────
            $table->timestamp('submitted_at')->useCurrent();
            $table->timestamps();

            // ── Indexes ───────────────────────────────────────────────────
            $table->index('dealer_id');
            $table->index(['dealer_id', 'form_type']);
            $table->index(['dealer_id', 'status']);
            $table->index(['dealer_id', 'is_read']);
            $table->index(['dealer_id', 'submitted_at']);
            $table->index(['dealer_id', 'status', 'is_read']);
            $table->index('vehicle_id');
            $table->index(['dealer_id', 'first_name', 'last_name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('form_entries');
    }
};