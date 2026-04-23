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
        Schema::create('customer_reviews', function (Blueprint $table) {
            $table->id();
            $table->string('reviewer_name');
            $table->string('review_headline')->nullable();
            $table->date('review_date')->nullable();
            $table->string('review_source')->nullable();
            $table->integer('star_count')->nullable();
            $table->foreignId('customer_review_category_id')->nullable()->constrained()->onDelete('set null');
            $table->string('photo_url')->nullable();
            $table->text('content');
            $table->string('status')->default('Active');
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
        Schema::dropIfExists('customer_reviews');
    }
};
