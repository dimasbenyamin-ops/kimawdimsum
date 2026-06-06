<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: create_nav_menus_table
 *
 * Stores the backend sidebar navigation items. Named `nav_menus`
 * to avoid collision with the existing `menus` (food items) table.
 *
 * Each nav_menu entry corresponds to a backend route/section that
 * the role_menu pivot table maps to a specific Role.
 *
 * Example entries:
 *   - User Management  → route: admin.users.index
 *   - Menu Makanan     → route: admin.menus.index
 *   - Role             → route: admin.roles.index
 *   - Role Menu        → route: admin.role-menu.index
 *   - Dashboard Kasir  → route: cashier.dashboard
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nav_menus', function (Blueprint $table) {
            $table->id();

            $table->string('name')->comment('Display label shown in sidebar, e.g. "User"');

            // Route name used for permission checks and link generation
            $table->string('route_name')
                  ->unique()
                  ->comment('Named route, e.g. "admin.users.index"');

            $table->string('icon')
                  ->nullable()
                  ->comment('Optional icon class, e.g. "fa fa-users"');

            // Hierarchical support for grouped/sub-menus
            $table->unsignedBigInteger('parent_id')
                  ->nullable()
                  ->comment('Parent nav_menu ID for grouped menus (null = top-level)');

            $table->unsignedSmallInteger('sort_order')
                  ->default(0)
                  ->comment('Display order in the sidebar');

            $table->timestamps();

            // Self-referential FK
            $table->foreign('parent_id')
                  ->references('id')
                  ->on('nav_menus')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nav_menus');
    }
};
