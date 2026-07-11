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
            $table->text('url')->nullable()->change();
            $table->text('referrer')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('website_visitor_logs', function (Blueprint $table) {
            $table->string('url')->nullable()->change();
            $table->string('referrer')->nullable()->change();
        });
    }
};
