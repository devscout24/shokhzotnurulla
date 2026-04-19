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
        Schema::create('location_hours', function (Blueprint $table) {
            $table->id();

            $table->morphs('hourly'); // location, maybe other models later
            $table->string('department'); // sales, service, parts, rentals, collision
            $table->unsignedTinyInteger('day_of_week'); // 1-7 (Monday=1)
            $table->time('open_time')->nullable();
            $table->time('close_time')->nullable();
            $table->boolean('is_closed')->default(false);
            $table->boolean('appointment_only')->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('location_hours');
    }
};
