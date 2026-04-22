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
        Schema::table('promo_banners', function (Blueprint $table) {
            $table->text('disclaimer')->nullable()->after('title');
            $table->string('condition')->nullable()->after('promo_category_id');
            $table->string('certified')->nullable()->after('condition');
            $table->string('desktop_image_url')->nullable()->after('link_url');
            $table->string('mobile_image_url')->nullable()->after('desktop_image_url');
            $table->string('srp_desktop_banner_url')->nullable()->after('mobile_image_url');
            $table->string('srp_mobile_banner_url')->nullable()->after('srp_desktop_banner_url');
            $table->string('primary_color')->nullable()->after('srp_mobile_banner_url');
            $table->string('secondary_color')->nullable()->after('primary_color');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('promo_banners', function (Blueprint $table) {
            $table->dropColumn([
                'disclaimer',
                'condition',
                'certified',
                'desktop_image_url',
                'mobile_image_url',
                'srp_desktop_banner_url',
                'srp_mobile_banner_url',
                'primary_color',
                'secondary_color',
            ]);
        });
    }
};
