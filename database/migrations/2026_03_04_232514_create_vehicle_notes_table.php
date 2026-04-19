<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicle_notes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('vehicle_id')
                  ->constrained()
                  ->cascadeOnDelete()
                  ->cascadeOnUpdate();

            // Public-facing description (dealer written or AI generated)
            $table->longText('dealer_notes')->nullable();
            $table->longText('ai_description')->nullable();

            // Internal use only (not shown on website)
            $table->longText('internal_notes')->nullable();

            // Highlights — JSON array of strings (key selling points)
            // e.g. ["Backup Camera", "Heated Seats"]
            $table->json('key_highlights')->nullable();

            // Categorized highlights from VIN decode
            // e.g. {"Safety and Security": [...], "Interior": [...]}
            $table->json('highlights')->nullable();
            $table->boolean('lock_highlights')->default(false);

            // Warranty information (for Buyer's Guide / printables)
            $table->string('warranty_dealer', 10)->nullable();   // "AS IS", "Full", "Limited"
            $table->string('warranty_non_dealer', 100)->nullable();
            $table->unsignedTinyInteger('warranty_labor')->nullable();   // percentage
            $table->unsignedTinyInteger('warranty_parts')->nullable();   // percentage
            $table->text('warranty_systems')->nullable();
            $table->string('warranty_duration', 100)->nullable();
            $table->boolean('service_contract')->default(false);

            $table->unique('vehicle_id');
            $table->timestamps();
            $table->softDeletes('deleted_at', precision: 0);
        });

        // MySQL FULLTEXT indexes
        DB::statement('ALTER TABLE vehicle_notes ADD FULLTEXT dealer_notes_fulltext (dealer_notes)');
        DB::statement('ALTER TABLE vehicle_notes ADD FULLTEXT ai_description_fulltext (ai_description)');
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicle_notes');
    }
};