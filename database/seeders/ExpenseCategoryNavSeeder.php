<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExpenseCategoryNavSeeder extends Seeder
{
    public function run(): void
    {
        // Temukan parent Pengaturan
        $setupParentId = DB::table('nav_menus')
            ->where('name', 'Pengaturan')
            ->whereNull('parent_id')
            ->value('id');

        $menu = [
            'name'       => 'Kategori Biaya',
            'icon'       => 'bi bi-tags',
            'route_name' => 'admin.expense-categories.index',
            'parent_id'  => $setupParentId,
            'sort_order' => 15, // Setelah Role Menu (13)
        ];

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

        // Attach ke Administrator (biasanya role_id = 1)
        $roles = [1];
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
        
        $this->command->info('✅ NavMenu for Expense Category seeded.');
    }
}
