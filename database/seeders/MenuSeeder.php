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
            // Atmosphere x Kumaw Dimsum - Bundling
            ['name' => '2 Dimsum Ori + 1 Coffee', 'price' => 25000, 'category' => 'bundling_hemat', 'description' => 'Atmosphere x Kumaw Dimsum Bundling'],
            ['name' => '3 Dimsum Ori + 1 Coffee', 'price' => 30000, 'category' => 'bundling_hemat', 'description' => 'Atmosphere x Kumaw Dimsum Bundling'],
            ['name' => '2 Dimsum Spicy Mayo + 1 Coffee', 'price' => 26000, 'category' => 'bundling_hemat', 'description' => 'Atmosphere x Kumaw Dimsum Bundling'],
            ['name' => '3 Dimsum Spicy Mayo + 1 Coffee', 'price' => 32000, 'category' => 'bundling_hemat', 'description' => 'Atmosphere x Kumaw Dimsum Bundling'],
            ['name' => '2 Dimsum Goreng Keju + 1 Coffee', 'price' => 48000, 'category' => 'bundling_hemat', 'description' => 'Atmosphere x Kumaw Dimsum Bundling'],

            // Atmosphere x Kumaw Dimsum - Better Together
            ['name' => '4 Dimsum Ori + 2 Coffee', 'price' => 48000, 'category' => 'bundling_hemat', 'description' => 'Atmosphere x Kumaw Dimsum Better Together'],
            ['name' => '6 Dimsum Ori + 2 Coffee', 'price' => 57000, 'category' => 'bundling_hemat', 'description' => 'Atmosphere x Kumaw Dimsum Better Together'],
            ['name' => '4 Dimsum Spicy Mayo + 2 Coffee', 'price' => 50000, 'category' => 'bundling_hemat', 'description' => 'Atmosphere x Kumaw Dimsum Better Together'],
            ['name' => '6 Dimsum Spicy Mayo + 2 Coffee', 'price' => 60000, 'category' => 'bundling_hemat', 'description' => 'Atmosphere x Kumaw Dimsum Better Together'],

            // Original
            ['name' => 'Dimsum Original 4 pcs', 'price' => 18000, 'category' => 'original', 'description' => 'Include Saus Bangkok'],
            ['name' => 'Dimsum Original 6 pcs', 'price' => 25000, 'category' => 'original', 'description' => 'Include Saus Bangkok'],
            ['name' => 'Dimsum Original 8 pcs', 'price' => 31000, 'category' => 'original', 'description' => 'Include Saus Bangkok'],
            
            // Spicy Mayo
            ['name' => 'Dimsum Spicy Mayo 4 pcs', 'price' => 20000, 'category' => 'spicy_mayo', 'description' => 'Include Chili Oil'],
            ['name' => 'Dimsum Spicy Mayo 6 pcs', 'price' => 27000, 'category' => 'spicy_mayo', 'description' => 'Include Chili Oil'],
            ['name' => 'Dimsum Spicy Mayo 8 pcs', 'price' => 34000, 'category' => 'spicy_mayo', 'description' => 'Include Chili Oil'],
            
            // Mix
            ['name' => 'Dimsum Mix 4 pcs (2 Ori + 2 Spicy Mayo)', 'price' => 19000, 'category' => 'original', 'description' => 'Include Chili Oil/Saus Bangkok'],
            ['name' => 'Dimsum Mix 6 pcs (3 Ori + 3 Spicy Mayo)', 'price' => 26000, 'category' => 'original', 'description' => 'Include Chili Oil/Saus Bangkok'],
            ['name' => 'Dimsum Mix 8 pcs (4 Ori + 4 Spicy Mayo)', 'price' => 33000, 'category' => 'original', 'description' => 'Include Chili Oil/Saus Bangkok'],
            ['name' => 'Trio Mix (2 Ori + 2 Spicy Mayo + 2 Gk)', 'price' => 30000, 'category' => 'original', 'description' => 'Include Chili Oil/Saus Bangkok'],

            // Goreng Keju
            ['name' => 'Dimsum Goreng Keju 3 pcs', 'price' => 18000, 'category' => 'goreng_keju', 'description' => 'Include Saus Bangkok'],
            ['name' => 'Dimsum Goreng Keju 5 pcs', 'price' => 29000, 'category' => 'goreng_keju', 'description' => 'Include Saus Bangkok'],

            // Premium Sauce
            ['name' => 'Tartar Sauce 4 pcs', 'price' => 22000, 'category' => 'premium_sauce', 'description' => 'Include Chili Oil'],
            ['name' => 'Tartar Sauce 6 pcs', 'price' => 30000, 'category' => 'premium_sauce', 'description' => 'Include Chili Oil'],
            ['name' => 'Tartar Sauce 8 pcs', 'price' => 37000, 'category' => 'premium_sauce', 'description' => 'Include Chili Oil'],
            ['name' => 'Garlic Mayo Sauce 4 pcs', 'price' => 22000, 'category' => 'premium_sauce', 'description' => 'Include Chili Oil'],
            ['name' => 'Garlic Mayo Sauce 6 pcs', 'price' => 30000, 'category' => 'premium_sauce', 'description' => 'Include Chili Oil'],
            ['name' => 'Garlic Mayo Sauce 8 pcs', 'price' => 37000, 'category' => 'premium_sauce', 'description' => 'Include Chili Oil'],
            ['name' => 'Carbonara Sauce 4 pcs', 'price' => 22000, 'category' => 'premium_sauce', 'description' => 'Include Chili Oil'],
            ['name' => 'Carbonara Sauce 6 pcs', 'price' => 30000, 'category' => 'premium_sauce', 'description' => 'Include Chili Oil'],
            ['name' => 'Carbonara Sauce 8 pcs', 'price' => 37000, 'category' => 'premium_sauce', 'description' => 'Include Chili Oil'],

            // Sharing Party
            ['name' => 'Mix Party 16 Pcs', 'price' => 85000, 'category' => 'sharing_party', 'description' => '8 Pcs Original + 8 Pcs Spicy Mayo (Kemasan Al-Foil + Box Ivory Premium, Sumpit 3 Pcs, 4 Sauces)'],
            ['name' => 'Spicy Mayo Party 16 Pcs', 'price' => 90000, 'category' => 'sharing_party', 'description' => '16 Pcs Spicy Mayo (Kemasan Al-Foil + Box Ivory Premium, Sumpit 3 Pcs, 4 Sauces)'],
            ['name' => 'Special Mix Party 16 Pcs', 'price' => 89000, 'category' => 'sharing_party', 'description' => '4 Pcs Original + 4 Pcs Spicy Mayo + 8 Pcs GK (Kemasan Al-Foil + Box Ivory Premium, Sumpit 3 Pcs, 4 Sauces)'],

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

            // Add On
            ['name' => 'Extra Chili Oil (1 cup)', 'price' => 3000, 'category' => 'add_on', 'description' => 'ADD ON saus'],
            ['name' => 'Saus Bangkok (1 cup)', 'price' => 3000, 'category' => 'add_on', 'description' => 'ADD ON saus'],
            ['name' => 'Topping Cheese Slice (1 pcs)', 'price' => 1000, 'category' => 'add_on', 'description' => 'ADD ON toping'],
            ['name' => 'Topping Beef Pepperoni (1 pcs)', 'price' => 3000, 'category' => 'add_on', 'description' => 'ADD ON toping'],
            ['name' => 'Torch Saus Spicy Mayo (GK) (1 pcs)', 'price' => 1000, 'category' => 'add_on', 'description' => 'ADD ON toping'],
        ];

        $sortOrder = 1;
        foreach ($menus as $data) {
            $slug = Str::slug($data['name']);
            $menu = Menu::withTrashed()->where('slug', $slug)->first();
            
            $imagePath = null;
            if ($data['name'] === 'Dimsum Original 4 pcs') {
                $imagePath = 'images/ori isi 4.png';
            } elseif ($data['name'] === 'Dimsum Original 6 pcs') {
                $imagePath = 'images/ori isi 6.png';
            } elseif ($data['name'] === 'Dimsum Original 8 pcs') {
                $imagePath = 'images/ori isi 8.png';
            } elseif ($data['name'] === 'Dimsum Spicy Mayo 4 pcs') {
                $imagePath = 'images/sm isi 4.png';
            } elseif ($data['name'] === 'Dimsum Spicy Mayo 6 pcs') {
                $imagePath = 'images/sm isi 6.png';
            } elseif ($data['name'] === 'Dimsum Spicy Mayo 8 pcs') {
                $imagePath = 'images/sm isi 8.png';
            } elseif ($data['name'] === 'Dimsum Mix 4 pcs (2 Ori + 2 Spicy Mayo)') {
                $imagePath = 'images/mix isi 4.png';
            } elseif ($data['name'] === 'Dimsum Mix 6 pcs (3 Ori + 3 Spicy Mayo)') {
                $imagePath = 'images/mix isi 6.png';
            } elseif ($data['name'] === 'Dimsum Mix 8 pcs (4 Ori + 4 Spicy Mayo)') {
                $imagePath = 'images/mix isi 8.png';
            } elseif ($data['name'] === 'Trio Mix (2 Ori + 2 Spicy Mayo + 2 Gk)') {
                $imagePath = 'images/trio mix.png';
            } elseif ($data['name'] === 'Mix Party 16 Pcs') {
                $imagePath = 'images/mix partysize.png';
            } elseif ($data['category'] === 'party') {
                $imagePath = 'images/partysize.png';
            } elseif ($data['category'] === 'snacks') {
                $imagePath = 'images/mixplatter.png';
            } elseif ($data['category'] === 'goreng_keju') {
                $imagePath = 'images/goreng keju.png';
            } elseif ($data['category'] === 'add_on') {
                $imagePath = 'images/sauce.png';
            }

            if ($menu) {
                if ($menu->trashed()) {
                    $menu->restore();
                }
                $menu->update([
                    'name'         => $data['name'],
                    'description'  => $data['description'],
                    'category'     => $data['category'],
                    'price'        => $data['price'],
                    'image_path'   => $imagePath,
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
                    'image_path'   => $imagePath,
                    'is_available' => true,
                    'sort_order'   => $sortOrder++,
                    'created_by'   => null,
                ]);
            }
        }

        $this->command->info('✅ MenuSeeder: ' . count($menus) . ' new menu items seeded.');
    }
}
