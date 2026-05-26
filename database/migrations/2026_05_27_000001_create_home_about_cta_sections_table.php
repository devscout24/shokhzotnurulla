<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('home_about_cta_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dealer_id')->constrained('dealers')->onDelete('cascade');
            $table->json('content');
            $table->timestamps();

            $table->unique('dealer_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('home_about_cta_sections');
    }
};
