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
        Schema::create('menus', function (Blueprint $table) {
            $table->id();

            $table->foreignId('dealer_id')
                  ->constrained()
                  ->cascadeOnUpdate()
                  ->cascadeOnDelete();

            $table->unsignedBigInteger('parent_id')->nullable();
            $table->foreign('parent_id')
                  ->references('id')
                  ->on('menus')
                  ->nullOnDelete();

            $table->enum('location', ['main', 'footer']);
            $table->string('label');
            $table->string('url');
            $table->string('target')->default('_self');
            $table->unsignedInteger('sort_order')->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menus');
    }
};
