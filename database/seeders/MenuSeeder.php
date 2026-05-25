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
        $menus = [
            [
                'location'   => 'main',
                'label'      => 'Inventory',
                'url'        => '#',
                'target'     => '_self',
                'sort_order' => 0,
                'children'   => [
                    ['label' => 'All Inventory', 'url' => '/inventory', 'target' => '_self', 'sort_order' => 0],
                    ['label' => 'Cars', 'url' => '/cars', 'target' => '_self', 'sort_order' => 1],
                    ['label' => 'Trucks', 'url' => '/trucks', 'target' => '_self', 'sort_order' => 2],
                    ['label' => 'SUVs', 'url' => '/suvs', 'target' => '_self', 'sort_order' => 3],
                    ['label' => 'Vans', 'url' => '/vans', 'target' => '_self', 'sort_order' => 4],
                    ['label' => 'Convertibles', 'url' => '/convertibles', 'target' => '_self', 'sort_order' => 5],
                    ['label' => 'Hatchbacks', 'url' => '/hatchbacks', 'target' => '_self', 'sort_order' => 6],
                ],
            ],
            [
                'location'   => 'main',
                'label'      => 'Finance',
                'url'        => '#',
                'target'     => '_self',
                'sort_order' => 1,
                'children'   => [
                    ['label' => 'Get Approved', 'url' => '/get-approved', 'target' => '_self', 'sort_order' => 0],
                ],
            ],
            [
                'location'   => 'main',
                'label'      => 'Service',
                'url'        => '/schedule-service',
                'target'     => '_self',
                'sort_order' => 2,
                'children'   => [],
            ],
            [
                'location'   => 'main',
                'label'      => 'About',
                'url'        => '#',
                'target'     => '_self',
                'sort_order' => 3,
                'children'   => [
                    ['label' => 'About Us', 'url' => '/about-us', 'target' => '_self', 'sort_order' => 0],
                    ['label' => 'Contact Us', 'url' => '/contact-us', 'target' => '_self', 'sort_order' => 1],
                ],
            ],
            [
                'location'   => 'footer',
                'label'      => 'View Inventory',
                'url'        => '/inventory',
                'target'     => '_self',
                'sort_order' => 0,
                'children'   => [],
            ],
            [
                'location'   => 'footer',
                'label'      => 'Direction',
                'url'        => '#',
                'target'     => '_blank',
                'sort_order' => 1,
                'children'   => [],
            ],
            [
                'location'   => 'footer',
                'label'      => 'About us',
                'url'        => '/about-us',
                'target'     => '_self',
                'sort_order' => 2,
                'children'   => [],
            ],
            [
                'location'   => 'footer',
                'label'      => 'Get approved',
                'url'        => '/get-approved',
                'target'     => '_self',
                'sort_order' => 3,
                'children'   => [],
            ],
            [
                'location'   => 'footer',
                'label'      => 'Contact us',
                'url'        => '/contact-us',
                'target'     => '_self',
                'sort_order' => 4,
                'children'   => [],
            ],
            [
                'location'   => 'footer',
                'label'      => 'Privacy policy',
                'url'        => '/privacy-policy',
                'target'     => '_self',
                'sort_order' => 5,
                'children'   => [],
            ],
            [
                'location'   => 'footer',
                'label'      => 'Terms of service',
                'url'        => '/terms-of-service',
                'target'     => '_self',
                'sort_order' => 6,
                'children'   => [],
            ],
        ];

        foreach ($menus as $menuData) {
            $parentMenu = Menu::create([
                'dealer_id'  => $dealer->id,
                'location'   => $menuData['location'],
                'label'      => $menuData['label'],
                'url'        => $menuData['url'],
                'target'     => $menuData['target'],
                'parent_id'  => null,
                'sort_order' => $menuData['sort_order'],
            ]);

            if (! empty($menuData['children'])) {
                foreach ($menuData['children'] as $childData) {
                    Menu::create([
                        'dealer_id'  => $dealer->id,
                        'location'   => $menuData['location'],
                        'label'      => $childData['label'],
                        'url'        => $childData['url'],
                        'target'     => $childData['target'] ?? '_self',
                        'parent_id'  => $parentMenu->id,
                        'sort_order' => $childData['sort_order'],
                    ]);
                }
            }
        }
    }
}
