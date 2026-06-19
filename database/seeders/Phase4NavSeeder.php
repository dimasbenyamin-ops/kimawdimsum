<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Phase4NavSeeder extends Seeder
{
    public function run(): void
    {
        // Temukan parent Inventori (jika ada)
        $inventoryParentId = DB::table('nav_menus')
            ->where('name', 'Inventori')
            ->whereNull('parent_id')
            ->value('id');

        // Buat parent "Laporan" jika belum ada
        $reportParentId = DB::table('nav_menus')
            ->where('name', 'Laporan')
            ->whereNull('parent_id')
            ->value('id');

        if (!$reportParentId) {
            $reportParentId = DB::table('nav_menus')->insertGetId([
                'name'       => 'Laporan Keuangan',
                'icon'       => 'bi bi-graph-up-arrow',
                'route_name' => 'admin.reports.profit-loss',
                'parent_id'  => null,
                'sort_order' => 50,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $menus = [
            [
                'name'       => 'Stock Opname',
                'icon'       => 'bi bi-clipboard-check',
                'route_name' => 'admin.stock-opnames.index',
                'parent_id'  => $inventoryParentId,
                'sort_order' => 30,
            ],
            [
                'name'       => 'Biaya Operasional',
                'icon'       => 'bi bi-cash-stack',
                'route_name' => 'admin.expenses.index',
                'parent_id'  => $reportParentId,
                'sort_order' => 5,
            ],
            [
                'name'       => 'Laba Rugi (P&L)',
                'icon'       => 'bi bi-graph-up-arrow',
                'route_name' => 'admin.reports.profit-loss',
                'parent_id'  => $reportParentId,
                'sort_order' => 10,
            ],
            [
                'name'       => 'Analisis HPP & Margin',
                'icon'       => 'bi bi-calculator',
                'route_name' => 'admin.reports.cogs',
                'parent_id'  => $reportParentId,
                'sort_order' => 15,
            ],
            [
                'name'       => 'Valuasi Stok Gudang',
                'icon'       => 'bi bi-box-seam',
                'route_name' => 'admin.reports.stock-audit',
                'parent_id'  => $reportParentId,
                'sort_order' => 20,
            ],
            [
                'name'       => 'Penjualan per Menu',
                'icon'       => 'bi bi-bar-chart-line',
                'route_name' => 'admin.reports.sales-per-menu',
                'parent_id'  => $reportParentId,
                'sort_order' => 25,
            ],
            [
                'name'       => 'Arus Kas (Cash Flow)',
                'icon'       => 'bi bi-currency-exchange',
                'route_name' => 'admin.reports.cash-flow',
                'parent_id'  => $reportParentId,
                'sort_order' => 30,
            ],
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
