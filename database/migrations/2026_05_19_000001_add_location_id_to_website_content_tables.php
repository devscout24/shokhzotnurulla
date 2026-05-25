<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tables that have a dealer_id column — add location_id after dealer_id.
     */
    private array $dealerTables = [
        'blog_posts',
        'pages',
        'faqs',
        'menus',
        'media',
        'slides',
        'srp_contents',
        'static_page_contents',
    ];

    /**
     * Tables that DON'T have dealer_id — add location_id after id.
     */
    private array $nonDealerTables = [
        'staff_members',
        'service_offers',
        'promo_banners',
        'job_posts',
        'customer_reviews',
        'events',
    ];

    public function up(): void
    {
        foreach ($this->dealerTables as $table) {
            if (Schema::hasTable($table) && !Schema::hasColumn($table, 'location_id')) {
                Schema::table($table, function (Blueprint $t) {
                    $t->unsignedBigInteger('location_id')->nullable()->after('dealer_id');
                    $t->foreign('location_id')->references('id')->on('locations')->onDelete('set null');
                    $t->index('location_id');
                });
            }
        }

        foreach ($this->nonDealerTables as $table) {
            if (Schema::hasTable($table) && !Schema::hasColumn($table, 'location_id')) {
                Schema::table($table, function (Blueprint $t) {
                    $t->unsignedBigInteger('location_id')->nullable()->after('id');
                    $t->foreign('location_id')->references('id')->on('locations')->onDelete('set null');
                    $t->index('location_id');
                });
            }
        }
    }

    public function down(): void
    {
        $allTables = array_merge($this->dealerTables, $this->nonDealerTables);

        foreach ($allTables as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'location_id')) {
                Schema::table($table, function (Blueprint $t) {
                    $t->dropForeign(['location_id']);
                    $t->dropColumn('location_id');
                });
            }
        }
    }
};
