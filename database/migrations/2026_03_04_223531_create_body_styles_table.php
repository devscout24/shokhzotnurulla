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
        Schema::create('body_styles', function (Blueprint $table) {
            $table->id();

            $table->foreignId('body_style_group_id')
                  ->constrained('body_style_groups')
                  ->cascadeOnDelete()
                  ->cascadeOnUpdate();

            $table->string('name', 100);
            $table->string('slug')->unique();

            $table->unique(['body_style_group_id', 'name']);

            $table->timestamps();
            $table->softDeletes('deleted_at', precision: 0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('body_styles');
    }
};
