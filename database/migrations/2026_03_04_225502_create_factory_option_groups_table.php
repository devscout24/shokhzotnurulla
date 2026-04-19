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
        Schema::create('factory_option_groups', function (Blueprint $table) {
            $table->id();

            $table->foreignId('category_id')
                  ->constrained('factory_option_categories')
                  ->cascadeOnDelete()
                  ->cascadeOnUpdate();

            $table->string('name', 100);

            $table->unique(['category_id', 'name']);

            $table->timestamps();
            $table->softDeletes('deleted_at', precision: 0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('factory_option_groups');
    }
};
