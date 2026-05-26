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

            $table->foreignId('banner_desktop_media_id')->nullable()->constrained('media')->nullOnDelete();
            $table->foreignId('banner_mobile_media_id')->nullable()->constrained('media')->nullOnDelete();
            
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
