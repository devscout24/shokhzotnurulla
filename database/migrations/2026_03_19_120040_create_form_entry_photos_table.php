<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('form_entry_photos', function (Blueprint $table) {

            $table->id();

            $table->foreignId('form_entry_id')
                  ->constrained('form_entries')
                  ->cascadeOnDelete();

            $table->string('path');
            $table->string('disk')->default('public');
            $table->string('url');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index('form_entry_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('form_entry_photos');
    }
};
