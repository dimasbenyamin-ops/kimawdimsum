<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Phase3NavSeeder extends Seeder
{
    public function run(): void
    {
        // Temukan parent Inventori (jika ada)
        $parentId = DB::table('nav_menus')
            ->where('name', 'Inventori')
            ->whereNull('parent_id')
            ->value('id');

        $menus = [
            [
                'name'       => 'Pembelian',
                'icon'       => 'bi bi-cart4',
                'route_name' => 'admin.purchases.index',
                'parent_id'  => $parentId,
                'sort_order' => 20,
            ],
            [
                'name'       => 'Pencatatan Waste',
                'icon'       => 'bi bi-trash3',
                'route_name' => 'admin.waste.index',
                'parent_id'  => $parentId,
                'sort_order' => 25,
            ]
        ];

        foreach ($menus as $menu) {
            $menuId = DB::table('nav_menus')->where('route_name', $menu['route_name'])->value('id');

            if (!$menuId) {
                $menuId = DB::table('nav_menus')->insertGetId([
                    'name'       => $menu['name'],
                    'icon'       => $menu['icon'],
                    'route_name' => $menu['route_name'],
                    'parent_id'  => $menu['parent_id'],
                    'sort_order' => $menu['sort_order'],
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
}
