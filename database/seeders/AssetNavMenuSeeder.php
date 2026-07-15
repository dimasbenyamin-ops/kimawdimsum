<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AssetNavMenuSeeder extends Seeder
{
    public function run(): void
    {
        // Temukan parent Inventori (biasanya route_name = '#' dan namanya 'Inventori' atau 'Inventory')
        $parent = DB::table('nav_menus')->where('name', 'Inventori')->first();
        
        $parentId = $parent ? $parent->id : null;

        // Cek apakah menu sudah ada
        $exists = DB::table('nav_menus')->where('route_name', 'admin.assets.index')->exists();

        if (!$exists) {
            $menuId = DB::table('nav_menus')->insertGetId([
                'name' => 'Aset',
                'route_name' => 'admin.assets.index',
                'icon' => 'bi bi-archive',
                'parent_id' => $parentId,
                'sort_order' => 10,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Berikan akses otomatis ke Administrator (role_id = 1)
            DB::table('role_menu')->insert([
                'role_id' => 1,
                'nav_menu_id' => $menuId,
            ]);

            $this->command->info("NavMenu 'Aset' created successfully under Inventori parent.");
        } else {
            $this->command->info("NavMenu 'Aset' already exists.");
        }
    }
}
