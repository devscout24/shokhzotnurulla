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
        Schema::table('website_visitor_logs', function (Blueprint $table) {
            $table->string('referrer')->nullable();
            $table->string('utm_source')->nullable();
            $table->string('utm_medium')->nullable();
            $table->string('utm_campaign')->nullable();
            $table->string('session_id')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('website_visitor_logs', function (Blueprint $table) {
            $table->dropColumn(['referrer', 'utm_source', 'utm_medium', 'utm_campaign', 'session_id']);
        });
    }
};
