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
        Schema::create('factory_options', function (Blueprint $table) {
            $table->id();

            $table->foreignId('category_id')
                  ->constrained('factory_option_categories')
                  ->cascadeOnDelete()
                  ->cascadeOnUpdate();

            $table->foreignId('group_id')
                  ->nullable()
                  ->constrained('factory_option_groups')
                  ->nullOnDelete()
                  ->cascadeOnUpdate();

            $table->string('option_key', 150);
            $table->string('label', 200);
            $table->string('sub_label', 150)->nullable();

            $table->unique(['category_id', 'option_key']);

            $table->timestamps();
            $table->softDeletes('deleted_at', precision: 0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('factory_options');
    }
};
