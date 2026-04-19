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
        Schema::create('redirects', function (Blueprint $table) {
            $table->id();

            $table->foreignId('dealer_id')
                ->constrained()
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->string('source_url');
            $table->string('target_url');
            $table->boolean('is_regex')->default(false);
            $table->unsignedSmallInteger('status_code')->default(301);
            $table->boolean('is_enabled')->default(true);

            $table->timestamps();

            $table->unique(['dealer_id', 'source_url', 'is_regex']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('redirects');
    }
};
