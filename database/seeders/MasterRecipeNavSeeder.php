<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MasterRecipeNavSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Temukan parent Inventori (jika ada)
        $parentId = DB::table('nav_menus')
            ->where('name', 'Inventori')
            ->whereNull('parent_id')
            ->value('id');

        // Jika tidak ada Inventori, coba cari parent Menu (sebagai fallback)
        if (!$parentId) {
            $parentId = DB::table('nav_menus')
                ->where('name', 'Menu')
                ->whereNull('parent_id')
                ->value('id');
        }

        $routeName = 'admin.master-recipes.index';
        $menuId = DB::table('nav_menus')->where('route_name', $routeName)->value('id');

        if (!$menuId) {
            $menuId = DB::table('nav_menus')->insertGetId([
                'name'       => 'Master Resep',
                'icon'       => 'bi bi-journal-text',
                'route_name' => $routeName,
                'parent_id'  => $parentId,
                'sort_order' => 15,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Attach ke Super Admin (role_id = 1) dan Admin (role_id = 2) jika ada
        $roles = [1, 2];
        foreach ($roles as $roleId) {
            $roleExists = DB::table('roles')->where('id', $roleId)->exists();
            if ($roleExists) {
                $exists = DB::table('role_menu')
                    ->where('role_id', $roleId)
                    ->where('nav_menu_id', $menuId)
                    ->exists();

                if (!$exists) {
                    DB::table('role_menu')->insert([
                        'role_id'     => $roleId,
                        'nav_menu_id' => $menuId,
                    ]);
                }
            }
        }
    }
}
