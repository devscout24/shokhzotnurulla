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
            $table->string('banner_text')->nullable()->after('optional_disclaimer');
            $table->string('banner_hover_title')->nullable()->after('banner_text');
            $table->string('banner_text_color')->nullable()->default('#ffffff')->after('banner_hover_title');
            $table->string('banner_bg_color')->nullable()->default('#c0392b')->after('banner_text_color');
            $table->unsignedBigInteger('banner_desktop_media_id')->nullable()->after('banner_bg_color');
            $table->unsignedBigInteger('banner_mobile_media_id')->nullable()->after('banner_desktop_media_id');

            // optional: foreign key constraints
            $table->foreign('banner_desktop_media_id')->references('id')->on('media')->onDelete('set null');
            $table->foreign('banner_mobile_media_id')->references('id')->on('media')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dealers', function (Blueprint $table) {
            $table->dropForeign(['banner_desktop_media_id']);
            $table->dropForeign(['banner_mobile_media_id']);
            $table->dropColumn([
                'banner_text',
                'banner_hover_title',
                'banner_text_color',
                'banner_bg_color',
                'banner_desktop_media_id',
                'banner_mobile_media_id',
            ]);
        });
    }
};
