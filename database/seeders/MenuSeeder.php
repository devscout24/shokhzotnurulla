<?php

namespace Database\Seeders;

use App\Models\Dealership\Dealer;
use App\Models\Website\Menu;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Seed for all existing dealers that don't have menus yet
        $dealers = Dealer::whereDoesntHave('menus')->get();

        foreach ($dealers as $dealer) {
            self::seedForDealer($dealer);
        }
    }

    /**
     * Seed default menus for a specific dealer.
     * Can be called from Observers or Controllers for newly registered dealers.
     */
    public static function seedForDealer(Dealer $dealer): void
    {
        // Create parent "Inventory" menu
        $inventoryMenu = Menu::create([
            'dealer_id'  => $dealer->id,
            'location'   => 'main',
            'label'      => 'Inventory',
            'url'        => '/inventory',
            'target'     => '_self',
            'parent_id'  => null,
            'sort_order' => 0,
        ]);

        // Create child "All Inventory" menu
        Menu::create([
            'dealer_id'  => $dealer->id,
            'location'   => 'main',
            'label'      => 'All Inventory',
            'url'        => '/inventory',
            'target'     => '_self',
            'parent_id'  => $inventoryMenu->id,
            'sort_order' => 0,
        ]);

        // Create child "Cars" menu
        Menu::create([
            'dealer_id'  => $dealer->id,
            'location'   => 'main',
            'label'      => 'Cars',
            'url'        => '/cars',
            'target'     => '_self',
            'parent_id'  => $inventoryMenu->id,
            'sort_order' => 1,
        ]);
    }
}
