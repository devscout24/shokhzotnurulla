<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicle_videos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('vehicle_id')
                  ->constrained()
                  ->cascadeOnDelete()
                  ->cascadeOnUpdate();

            $table->string('source', 30)->nullable();       // "youtube", "vimeo"
            $table->string('url', 500)->nullable();
            $table->boolean('autoplay')->default(false);
            $table->string('aspect_ratio', 10)->nullable(); // "16:9", "4:3"

            $table->unique('vehicle_id');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicle_videos');
    }
};