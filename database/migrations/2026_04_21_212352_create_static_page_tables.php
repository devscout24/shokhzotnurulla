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
        Schema::create('static_page_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dealer_id')->constrained('dealers')->onDelete('cascade');
            $table->string('name');
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['dealer_id', 'name']);
            $table->index('dealer_id');
        });

        Schema::create('static_page_contents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dealer_id')->constrained('dealers')->onDelete('cascade');
            $table->foreignId('static_page_category_id')->nullable()->constrained('static_page_categories')->onDelete('set null');
            $table->string('nickname')->nullable(); // For identification in list
            $table->string('slug');
            $table->string('h1_override')->nullable();
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('placement')->default('top');
            $table->longText('content')->nullable();
            $table->string('author')->nullable();
            $table->enum('status', ['Published', 'Draft'])->default('Published');
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['dealer_id', 'static_page_category_id'], 'spc_dealer_cat_index');
            $table->index(['dealer_id', 'status'], 'spc_dealer_status_index');
            $table->index(['dealer_id', 'slug'], 'spc_dealer_slug_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('static_page_contents');
        Schema::dropIfExists('static_page_categories');
    }
};
