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
        Schema::create('faq_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dealer_id')->constrained('dealers')->onDelete('cascade');
            $table->string('name');
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['dealer_id', 'name']);
            $table->index('dealer_id');
        });

        Schema::create('faqs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dealer_id')->constrained('dealers')->onDelete('cascade');
            $table->foreignId('faq_category_id')->nullable()->constrained('faq_categories')->onDelete('set null');
            $table->string('question');
            $table->longText('answer')->nullable();
            $table->string('author')->nullable();
            $table->enum('status', ['Published', 'Draft'])->default('Published');
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['dealer_id', 'faq_category_id']);
            $table->index(['dealer_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('faqs');
        Schema::dropIfExists('faq_categories');
    }
};
