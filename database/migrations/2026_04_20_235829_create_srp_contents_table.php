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
        Schema::create('srp_contents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dealer_id')->constrained('dealers')->onDelete('cascade');
            $table->string('nickname');
            $table->string('slug');
            $table->string('h1_override')->nullable();
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('placement')->default('bottom');
            $table->longText('content')->nullable();
            $table->string('author')->nullable();
            $table->enum('status', ['Published', 'Draft'])->default('Published');
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['dealer_id', 'status']);
            $table->index(['dealer_id', 'slug']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('srp_contents');
    }
};
