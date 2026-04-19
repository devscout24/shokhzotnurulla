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
            $table->string('slug', 100)->unique();

            $table->string('email')->nullable();
            $table->string('phone', 16)->nullable();

            $table->string('domain')->nullable()->unique();
            $table->string('staging_domain')->nullable()->unique();

            $table->boolean('is_active')->default(true)->index();

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
