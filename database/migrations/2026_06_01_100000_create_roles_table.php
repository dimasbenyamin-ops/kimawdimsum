<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: create_roles_table
 *
 * Stores named roles for internal staff users.
 * Roles:
 *   - Administrator : Full access to all backend menus
 *   - Kasir         : Access to order dashboard only
 *
 * Roles are managed by the Administrator through the backend.
 * Customers/guests do NOT have a role — they are unauthenticated.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique()->comment('Display name, e.g. "Administrator", "Kasir"');
            $table->string('guard_name')->default('web')->comment('Auth guard this role applies to');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
