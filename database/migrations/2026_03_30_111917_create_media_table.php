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
        Schema::create('media', function (Blueprint $table) {
            $table->id();

            $table->foreignId('dealer_id')
                  ->constrained()
                  ->cascadeOnUpdate()
                  ->cascadeOnDelete();

            $table->foreignId('user_id')
                  ->nullable()
                  ->constrained()
                  ->cascadeOnUpdate()
                  ->nullOnDelete();

            $table->string('original_name');          // user ka original filename
            $table->string('name');                   // stored filename (unique)
            $table->string('path');                   // storage path
            $table->string('disk')->default('public');
            $table->string('url');                    // public URL
            $table->string('title')->nullable();
            $table->string('alt_text')->nullable();
            $table->enum('type', ['image', 'video', 'file']);
            $table->string('mime_type');
            $table->unsignedBigInteger('size');       // bytes
            $table->unsignedInteger('width')->nullable();
            $table->unsignedInteger('height')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media');
    }
};
