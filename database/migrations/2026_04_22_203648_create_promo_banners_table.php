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
        Schema::create('promo_banners', function (Blueprint $table) {
            $table->id();
            $table->foreignId('promo_category_id')->nullable()->constrained()->onDelete('set null');
            $table->string('condition')->nullable();
            $table->string('certified')->nullable();

            $table->string('title');
            $table->text('disclaimer')->nullable();

            $table->string('author')->nullable();
            $table->string('status')->default('Active'); // Active, Inactive
            $table->dateTime('start_date')->nullable();
            $table->dateTime('end_date')->nullable();
            $table->string('link_url')->nullable();
            $table->string('desktop_image_url')->nullable();
            $table->string('mobile_image_url')->nullable();

            $table->string('srp_desktop_banner_url')->nullable();
            $table->string('srp_mobile_banner_url')->nullable();
            $table->string('primary_color')->nullable();
            $table->string('secondary_color')->nullable();
            
            $table->text('content')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promo_banners');
    }
};
