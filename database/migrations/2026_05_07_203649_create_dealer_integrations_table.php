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
        Schema::create('dealer_integrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dealer_id')->constrained()->onDelete('cascade');
            $table->string('provider')->index(); // e.g., carfax, stripe, 700credit
            $table->text('settings'); // Will store JSON encrypted at rest
            $table->boolean('is_active')->default(false);
            $table->timestamp('last_connected_at')->nullable();
            $table->timestamps();
            
            $table->unique(['dealer_id', 'provider']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dealer_integrations');
    }
};
