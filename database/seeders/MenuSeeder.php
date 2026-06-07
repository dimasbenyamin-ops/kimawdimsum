<?php

namespace Database\Seeders;

use App\Models\Menu;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MenuSeeder extends Seeder
{
    /**
     * Seed the menus table with the new categories and items.
     */
    public function run(): void
    {
        // Mark all existing menus as unavailable so they don't show up
        Menu::query()->update(['is_available' => false]);

        $menus = [
            // Original
            ['name' => 'Dimsum Original 4 pcs', 'price' => 17000, 'category' => 'original', 'description' => ''],
            ['name' => 'Dimsum Original 6 pcs', 'price' => 24000, 'category' => 'original', 'description' => ''],
            ['name' => 'Dimsum Original 8 pcs', 'price' => 30000, 'category' => 'original', 'description' => ''],
            
            // Spicy Mayo
            ['name' => 'Dimsum Spicy Mayo 4 pcs', 'price' => 20000, 'category' => 'spicy_mayo', 'description' => ''],
            ['name' => 'Dimsum Spicy Mayo 6 pcs', 'price' => 27000, 'category' => 'spicy_mayo', 'description' => ''],
            ['name' => 'Dimsum Spicy Mayo 8 pcs', 'price' => 35000, 'category' => 'spicy_mayo', 'description' => ''],
            
            // Mix
            ['name' => 'Dimsum Mix 4 pcs', 'price' => 19000, 'category' => 'original', 'description' => ''],
            ['name' => 'Dimsum Mix 6 pcs', 'price' => 27000, 'category' => 'original', 'description' => ''],
            ['name' => 'Dimsum Mix 8 pcs', 'price' => 35000, 'category' => 'original', 'description' => ''],
            ['name' => 'Trio Mix 4 pcs + 2 pcs', 'price' => 33000, 'category' => 'original', 'description' => ''],

            // Goreng Keju
            ['name' => 'Dimsum Goreng Keju 3 pcs', 'price' => 20000, 'category' => 'goreng_keju', 'description' => ''],
            ['name' => 'Dimsum Goreng Keju 5 pcs', 'price' => 30000, 'category' => 'goreng_keju', 'description' => ''],

            // Premium Sauce
            ['name' => 'Premium Sauce (Tartar, Carbonara, Garlic Mayo) 4 pcs', 'price' => 20000, 'category' => 'premium_sauce', 'description' => 'Tartar, Carbonara, Garlic Mayo'],
            ['name' => 'Premium Sauce (Tartar, Carbonara, Garlic Mayo) 6 pcs', 'price' => 30000, 'category' => 'premium_sauce', 'description' => 'Tartar, Carbonara, Garlic Mayo'],
            ['name' => 'Premium Sauce (Tartar, Carbonara, Garlic Mayo) 8 pcs', 'price' => 38000, 'category' => 'premium_sauce', 'description' => 'Tartar, Carbonara, Garlic Mayo'],

            // Sharing Party
            ['name' => 'Mix Party 16 Pcs', 'price' => 85000, 'category' => 'sharing_party', 'description' => ''],
            ['name' => 'Spicy Mayo Party 16 Pcs', 'price' => 90000, 'category' => 'sharing_party', 'description' => ''],
            ['name' => 'Special Mix Party 15 Pcs', 'price' => 89000, 'category' => 'sharing_party', 'description' => ''],
            
            // Add On
            ['name' => 'Chili Oil (1 cup)', 'price' => 3000, 'category' => 'add_on', 'description' => 'ADD ON saus'],
            ['name' => 'Saus Bangkok (1 cup)', 'price' => 3000, 'category' => 'add_on', 'description' => 'ADD ON saus'],
            ['name' => 'Cheese Melt (1 pcs/dimsum)', 'price' => 2000, 'category' => 'add_on', 'description' => 'ADD ON toping'],
            ['name' => 'Pepperoni (1 pcs/dimsum)', 'price' => 4000, 'category' => 'add_on', 'description' => 'ADD ON toping'],

            // Snacks
            ['name' => 'Chicken Gohiong', 'price' => 20000, 'category' => 'snacks', 'description' => ''],
            ['name' => 'Mix Platter (French Fries, Sausage, Chicken Pop)', 'price' => 22000, 'category' => 'snacks', 'description' => ''],
            ['name' => 'French Fries', 'price' => 18000, 'category' => 'snacks', 'description' => ''],

            // Minuman (Milk Based Coffee)
            ['name' => 'Salty', 'price' => 18000, 'category' => 'minuman', 'description' => 'Milk Based Coffee (Caffe Latte, Salted Caramel, Vanilla)'],
            ['name' => 'Ruma', 'price' => 18000, 'category' => 'minuman', 'description' => 'Milk Based Coffee (Caffe Latte, Vanilla, Rum)'],
            ['name' => 'Arenova', 'price' => 18000, 'category' => 'minuman', 'description' => 'Milk Based Coffee (Caffe Latte, Palm Sugar)'],
            ['name' => 'Buscotch', 'price' => 20000, 'category' => 'minuman', 'description' => 'Milk Based Coffee (Caffe Latte, Butterscotch Condensed Milk)'],
            ['name' => 'Cobans', 'price' => 20000, 'category' => 'minuman', 'description' => 'Milk Based Coffee (Caffe Latte, Banana Flavor, Vanilla)'],

            // Minuman (Black Series)
            ['name' => 'Genius Black', 'price' => 19000, 'category' => 'minuman', 'description' => 'Black Series (Grape Flavour, Espresso)'],
            ['name' => 'Citrush', 'price' => 17000, 'category' => 'minuman', 'description' => 'Black Series (Sparkling Orange Water, Espresso)'],
            ['name' => 'Black Florida', 'price' => 18000, 'category' => 'minuman', 'description' => 'Black Series (Orange Flavour, Espresso)'],
            ['name' => 'Fusion Americano', 'price' => 18000, 'category' => 'minuman', 'description' => 'Black Series (Palm Sugar, Vanilla, Espresso)'],
            ['name' => 'Berrycano', 'price' => 19000, 'category' => 'minuman', 'description' => 'Black Series (Strawberry Flavour, Espresso)'],
            ['name' => 'Mirage', 'price' => 18000, 'category' => 'minuman', 'description' => 'Black Series (Lychee Flavour, Espresso)'],

            // Minuman (Klasik)
            ['name' => 'The White', 'price' => 24000, 'category' => 'minuman', 'description' => 'Klasik (Caffe Latte)'],
            ['name' => 'The Dark', 'price' => 22000, 'category' => 'minuman', 'description' => 'Klasik (Americano)'],

            // Minuman (Non Coffee)
            ['name' => 'Golden Creamy', 'price' => 22000, 'category' => 'minuman', 'description' => 'Non Coffee (Milk, Yogurt, Orange Water, Jelly)'],
            ['name' => 'Chocolate', 'price' => 23000, 'category' => 'minuman', 'description' => 'Non Coffee (Dark Chocolate)'],
            ['name' => 'Matcha', 'price' => 23000, 'category' => 'minuman', 'description' => 'Non Coffee (Premium Matcha)'],
            ['name' => 'Havanas', 'price' => 15000, 'category' => 'minuman', 'description' => 'Non Coffee (Fresh Lime, Aromatic Rum)'],

            // Minuman (Tea Series)
            ['name' => 'Glow Tea', 'price' => 15000, 'category' => 'minuman', 'description' => 'Tea Series (Aromatic Rum, Tea, Lemon)'],
            ['name' => 'Strawberry Tea', 'price' => 15000, 'category' => 'minuman', 'description' => 'Tea Series (Aromatic Tea, Strawberry Flavour, Lemon)'],
            ['name' => 'Leci Tea', 'price' => 15000, 'category' => 'minuman', 'description' => 'Tea Series (Aromatic Tea, Lychee Flavour, Lemon)'],
            ['name' => 'Orange Tea', 'price' => 15000, 'category' => 'minuman', 'description' => 'Tea Series (Aromatic Tea, Orange Flavour, Lemon)'],
        ];

        $sortOrder = 1;
        foreach ($menus as $data) {
            $slug = Str::slug($data['name']);
            $menu = Menu::withTrashed()->where('slug', $slug)->first();
            
            if ($menu) {
                if ($menu->trashed()) {
                    $menu->restore();
                }
                $menu->update([
                    'name'         => $data['name'],
                    'description'  => $data['description'],
                    'category'     => $data['category'],
                    'price'        => $data['price'],
                    'is_available' => true,
                    'sort_order'   => $sortOrder++,
                ]);
            } else {
                Menu::create([
                    'name'         => $data['name'],
                    'slug'         => $slug,
                    'description'  => $data['description'],
                    'category'     => $data['category'],
                    'price'        => $data['price'],
                    'image_path'   => null,
                    'is_available' => true,
                    'sort_order'   => $sortOrder++,
                    'created_by'   => null,
                ]);
            }
        }

        $this->command->info('✅ MenuSeeder: ' . count($menus) . ' new menu items seeded.');
    }
}
