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
        Schema::create('service_offers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_offer_category_id')->nullable()->constrained()->onDelete('set null');
            $table->string('title');
            $table->string('subtitle')->nullable();
            $table->text('description');
            $table->string('photo_url')->nullable();
            $table->string('link_offer_to')->nullable();
            $table->string('link_text')->nullable();
            $table->text('disclaimer')->nullable();
            $table->string('status')->default('Published');
            $table->string('author')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_offers');
    }
};
