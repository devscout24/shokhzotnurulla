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
        $dealersWithLocations = \DB::table('locations')
            ->select('dealer_id')
            ->distinct()
            ->pluck('dealer_id');

        foreach ($dealersWithLocations as $dealerId) {
            $firstLocationId = \DB::table('locations')
                ->where('dealer_id', $dealerId)
                ->orderBy('order')
                ->value('id');

            if ($firstLocationId) {
                \DB::table('vehicles')
                    ->where('dealer_id', $dealerId)
                    ->whereNull('location_id')
                    ->update(['location_id' => $firstLocationId]);

                \DB::table('form_entries')
                    ->where('dealer_id', $dealerId)
                    ->whereNull('location_id')
                    ->update(['location_id' => $firstLocationId]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // One-way migration, reversing is not necessary or possible to reconstruct legacy state
    }
};
