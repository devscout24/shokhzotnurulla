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
        Schema::create('dealers', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->index();
            $table->string('company_name', 50)->index();
            $table->string('slug', 100)->unique();

            $table->string('internal_id', 10)->nullable()->unique();

            $table->string('email')->nullable();
            $table->string('phone', 16)->nullable();

            $table->string('domain')->nullable()->unique();
            $table->string('staging_domain')->nullable()->unique();

            $table->string('legal_name')->nullable();
            $table->text('corporate_address')->nullable();

            $table->string('support_email')->nullable();
            $table->unsignedSmallInteger('abandoned_form_minutes')->default(45);

            // Social links (JSON)
            $table->json('social_links')->nullable();

            // Disclaimers
            $table->text('finance_disclaimer')->nullable();
            $table->text('inventory_disclaimer')->nullable();
            $table->text('deposit_disclaimer')->nullable();
            $table->text('pricing_disclaimer')->nullable();
            $table->text('optional_disclaimer')->nullable();

            $table->string('banner_text')->nullable();
            $table->string('banner_hover_title')->nullable();
            $table->string('banner_text_color')->nullable()->default('#ffffff');
            $table->string('banner_bg_color')->nullable()->default('#c0392b');

            $table->boolean('is_active')->default(true)->index();
            $table->string('status')->default('active');

            $table->timestamps();
            $table->softDeletes('deleted_at', precision: 0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dealers');
    }
};
