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
        Schema::create('make_models', function (Blueprint $table) {
            $table->id();

             $table->foreignId('make_id')
                ->constrained('makes')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->string('name', 100);
            $table->string('slug')->unique(); // We will always make slug as make_slug_model_name_slug;

            $table->unique(['make_id', 'name']);

            $table->timestamps();
            $table->softDeletes('deleted_at', precision: 0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('make_models');
    }
};
