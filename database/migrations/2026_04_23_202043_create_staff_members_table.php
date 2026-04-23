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
        Schema::create('staff_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_member_category_id')->nullable()->constrained()->onDelete('set null');
            $table->string('full_name');
            $table->string('job_title');
            $table->string('photo_url')->nullable();
            $table->string('email_address')->nullable();
            $table->string('phone_number')->nullable();
            $table->text('short_bio')->nullable();
            $table->string('status')->default('Published');
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
        Schema::dropIfExists('staff_members');
    }
};
