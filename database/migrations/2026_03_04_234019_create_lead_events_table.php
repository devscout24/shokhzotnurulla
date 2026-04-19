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
        Schema::create('lead_events', function (Blueprint $table) {
            $table->id();

            $table->foreignId('vehicle_id')
                  ->constrained()
                  ->cascadeOnDelete()
                  ->cascadeOnUpdate();

            $table->foreignId('dealer_id')
                  ->constrained()
                  ->cascadeOnDelete()
                  ->cascadeOnUpdate();

            $table->string('type', 50);
            $table->string('source', 100)->nullable();

            $table->timestamps();
            $table->softDeletes('deleted_at', precision: 0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lead_events');
    }
};
