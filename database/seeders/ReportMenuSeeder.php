<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReportMenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $routeName = 'admin.reports.revenue';
        
        $menuId = DB::table('nav_menus')->where('route_name', $routeName)->value('id');
        
        if (!$menuId) {
            $menuId = DB::table('nav_menus')->insertGetId([
                'name' => 'Rekap Pendapatan',
                'route_name' => $routeName,
                'icon' => 'bi bi-cash-coin',
                'sort_order' => 5,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            DB::table('role_menu')->insertOrIgnore([
                'role_id' => 1,
                'nav_menu_id' => $menuId
            ]);
            
            $this->command->info('Menu "Rekap Pendapatan" inserted and linked to Administrator (Role 1).');
        } else {
            $this->command->info('Menu already exists in the database.');
        }
    }
}
