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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_category_id')->nullable()->constrained()->onDelete('set null');
            $table->string('title');
            $table->string('photo_url');
            $table->string('detail_link')->nullable();
            $table->string('registration_link')->nullable();
            $table->date('event_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->text('description');
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
        Schema::dropIfExists('events');
    }
};
