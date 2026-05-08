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
        Schema::table('dealer_integrations', function (Blueprint $table) {
            // Approval workflow columns
            $table->string('status')->default('draft')->after('is_active');
            // draft | pending_approval | approved | rejected | revoked

            $table->foreignId('approved_by')->nullable()->after('status')
                  ->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable()->after('approved_by');
            $table->text('rejection_reason')->nullable()->after('approved_at');

            // Audit: who last submitted
            $table->foreignId('submitted_by')->nullable()->after('rejection_reason')
                  ->constrained('users')->nullOnDelete();
            $table->timestamp('submitted_at')->nullable()->after('submitted_by');

            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dealer_integrations', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
            $table->dropForeign(['submitted_by']);
            $table->dropColumns([
                'status', 'approved_by', 'approved_at',
                'rejection_reason', 'submitted_by', 'submitted_at',
            ]);
        });
    }
};
