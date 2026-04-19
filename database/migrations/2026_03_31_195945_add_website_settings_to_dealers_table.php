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
        Schema::table('dealers', function (Blueprint $table) {
            // General settings
            $table->string('legal_name')->nullable()->after('staging_domain');
            $table->text('corporate_address')->nullable()->after('legal_name');
            $table->string('support_email')->nullable()->after('corporate_address');
            $table->unsignedSmallInteger('abandoned_form_minutes')->default(45)->after('support_email');

            // Social links (JSON)
            $table->json('social_links')->nullable()->after('abandoned_form_minutes');

            // Disclaimers
            $table->text('finance_disclaimer')->nullable()->after('social_links');
            $table->text('inventory_disclaimer')->nullable()->after('finance_disclaimer');
            $table->text('deposit_disclaimer')->nullable()->after('inventory_disclaimer');
            $table->text('pricing_disclaimer')->nullable()->after('deposit_disclaimer');
            $table->text('optional_disclaimer')->nullable()->after('pricing_disclaimer');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dealers', function (Blueprint $table) {
            $table->dropColumn([
                'legal_name', 'corporate_address', 'support_email', 'abandoned_form_minutes',
                'social_links',
                'finance_disclaimer', 'inventory_disclaimer', 'deposit_disclaimer',
                'pricing_disclaimer', 'optional_disclaimer',
            ]);
        });
    }
};
