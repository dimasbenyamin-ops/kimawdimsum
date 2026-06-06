<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: create_role_menu_table
 *
 * Pivot table mapping Roles to NavMenus (backend navigation sections).
 * If a Role has an entry in this table for a given NavMenu, users
 * with that Role may access that section of the backend dashboard.
 *
 * This enables dynamic, DB-driven authorization without code changes.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('role_menu', function (Blueprint $table) {
            // Composite primary key
            $table->unsignedBigInteger('role_id');
            $table->unsignedBigInteger('nav_menu_id');

            $table->primary(['role_id', 'nav_menu_id']);

            $table->foreign('role_id')
                  ->references('id')
                  ->on('roles')
                  ->cascadeOnDelete();

            $table->foreign('nav_menu_id')
                  ->references('id')
                  ->on('nav_menus')
                  ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('role_menu');
    }
};
