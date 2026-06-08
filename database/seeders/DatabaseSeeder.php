<?php

namespace Database\Seeders;

use App\Models\NavMenu;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * Order: Roles → NavMenus → RoleMenu pivot → Users → Food Menus
     */
    public function run(): void
    {
        // ─────────────────────────────────────────────────────────────────
        // 1. ROLES
        // ─────────────────────────────────────────────────────────────────
        $adminRole = Role::updateOrCreate(
            ['name' => 'Administrator'],
            ['guard_name' => 'web']
        );

        $kasirRole = Role::updateOrCreate(
            ['name' => 'Kasir'],
            ['guard_name' => 'web']
        );

        $this->command->info('✅ Roles seeded: Administrator, Kasir');

        // ─────────────────────────────────────────────────────────────────
        // 2. NAV MENUS (backend sidebar items)
        //    route_name values must match the named routes in web.php
        // ─────────────────────────────────────────────────────────────────
        $navDashboard = NavMenu::updateOrCreate(
            ['route_name' => 'admin.dashboard'],
            ['name' => 'Dashboard', 'icon' => 'bi bi-speedometer2', 'sort_order' => 0]
        );

        // Setup group (parent)
        $navSetup = NavMenu::updateOrCreate(
            ['route_name' => 'admin.setup'],
            ['name' => 'Pengaturan', 'icon' => 'bi bi-gear', 'sort_order' => 10, 'parent_id' => null]
        );

        $navUsers = NavMenu::updateOrCreate(
            ['route_name' => 'admin.users.index'],
            ['name' => 'User', 'icon' => 'bi bi-people', 'sort_order' => 11, 'parent_id' => $navSetup->id]
        );

        $navRoles = NavMenu::updateOrCreate(
            ['route_name' => 'admin.roles.index'],
            ['name' => 'Role', 'icon' => 'bi bi-shield-check', 'sort_order' => 12, 'parent_id' => $navSetup->id]
        );

        $navQris = NavMenu::updateOrCreate(
            ['route_name' => 'admin.settings.qris'],
            ['name' => 'Pengaturan QRIS', 'icon' => 'bi bi-qr-code', 'sort_order' => 14, 'parent_id' => $navSetup->id]
        );

        // NOTE: role-menu requires a {role} parameter so it cannot be linked bare
        // from the sidebar. We point this nav item to admin.roles.index instead,
        // which is the page where admins click "Role Menu" for each specific role.
        $navRoleMenu = NavMenu::updateOrCreate(
            ['route_name' => 'admin.roles.index'],
            ['name' => 'Role Menu', 'icon' => 'bi bi-list-check', 'sort_order' => 13, 'parent_id' => $navSetup->id]
        );

        $navFoodMenu = NavMenu::updateOrCreate(
            ['route_name' => 'admin.menus.index'],
            ['name' => 'Menu Makanan', 'icon' => 'bi bi-egg-fried', 'sort_order' => 20]
        );

        $navCashier = NavMenu::updateOrCreate(
            ['route_name' => 'cashier.dashboard'],
            ['name' => 'Dashboard Kasir', 'icon' => 'bi bi-receipt', 'sort_order' => 30]
        );

        $navPesanKasir = NavMenu::updateOrCreate(
            ['route_name' => 'cashier.order.create'],
            ['name' => 'Pesan di Kasir', 'icon' => 'bi bi-cart-plus', 'sort_order' => 25]
        );

        $navRevenueReport = NavMenu::updateOrCreate(
            ['route_name' => 'admin.reports.revenue'],
            ['name' => 'Rekap Pendapatan', 'icon' => 'bi bi-cash-coin', 'sort_order' => 35]
        );

        $this->command->info('✅ NavMenus seeded: Dashboard, Pengaturan, User, Role, Role Menu, Menu Makanan, Pesan di Kasir, Dashboard Kasir, Rekap Pendapatan');

        // ─────────────────────────────────────────────────────────────────
        // 3. ROLE-MENU ASSIGNMENTS
        //    Administrator → all menus
        //    Kasir         → Dashboard + Pesan di Kasir + Cashier Dashboard only
        // ─────────────────────────────────────────────────────────────────
        $adminRole->navMenus()->sync([
            $navDashboard->id,
            $navSetup->id,
            $navUsers->id,
            $navRoles->id,
            $navRoleMenu->id,
            $navFoodMenu->id,
            $navPesanKasir->id,
            $navCashier->id,
            $navRevenueReport->id,
        ]);

        $kasirRole->navMenus()->sync([
            $navDashboard->id,
            $navPesanKasir->id,
            $navCashier->id,
        ]);

        $this->command->info('✅ Role-Menu permissions seeded.');

        // ─────────────────────────────────────────────────────────────────
        // 4. DEFAULT STAFF USERS
        //    Password: password  (change after first login!)
        //    Customers / guests have NO user record.
        // ─────────────────────────────────────────────────────────────────
        User::updateOrCreate(
            ['email' => 'dimasbenyamin@gmail.com'],
            [
                'name'       => 'Admin Kumaw',
                'username'   => 'admin',
                'password'   => Hash::make('password'),
                'role_id'    => $adminRole->id,
                'phone'      => '087722472311',
                'expired_at' => null, // No expiry for default admin
            ]
        );

        User::updateOrCreate(
            ['email' => 'mutiarasabatina06@gmail.com'],
            [
                'name'       => 'Kasir Kumaw',
                'username'   => 'kasir',
                'password'   => Hash::make('password'),
                'role_id'    => $kasirRole->id,
                'phone'      => '082333216194',
                'expired_at' => null,
            ]
        );

        $this->command->info('✅ Default users seeded: admin (Administrator), kasir (Kasir)');
        $this->command->warn('   ⚠️  Default password is "password" — change it after first login!');

        // ─────────────────────────────────────────────────────────────────
        // 5. FOOD MENU ITEMS
        // ─────────────────────────────────────────────────────────────────
        $this->call([
            MenuSeeder::class,
        ]);
    }
}
