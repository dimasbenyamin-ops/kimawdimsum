<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: upgrade_users_for_rbac
 *
 * Changes to `users` table:
 *   - ADD `username`   — unique login handle for staff (separate from `name`)
 *   - ADD `role_id`    — FK to `roles` table (replaces the old `role` enum)
 *   - ADD `expired_at` — "Tanggal Exp User": date after which login is denied
 *   - DROP `role`      — old flat enum column removed
 *
 * Changes to `orders` table:
 *   - Make `user_id` NULLABLE to support guest checkout (unauthenticated orders)
 *
 * Guest customers do NOT have a user record; they place orders via session.
 * Internal staff (Admin, Kasir) always have a role_id assigned by the Admin.
 */
return new class extends Migration
{
    public function up(): void
    {
        // -------------------------------------------------------
        // users table: add RBAC columns
        // -------------------------------------------------------
        Schema::table('users', function (Blueprint $table) {
            // Username for staff login (nullable: guest/customer rows don't need it)
            $table->string('username', 50)
                  ->unique()
                  ->nullable()
                  ->after('name')
                  ->comment('Staff login handle. Null for guest/non-staff records.');

            // FK to new roles table (nullable: guest users have no role)
            $table->unsignedBigInteger('role_id')
                  ->nullable()
                  ->after('username')
                  ->comment('FK → roles.id. Null = guest/customer (no backend access).');

            // Expiry date — login denied after this date (fail-closed)
            $table->timestamp('expired_at')
                  ->nullable()
                  ->after('role_id')
                  ->comment('Tanggal Exp User: backend access denied after this datetime.');

            $table->foreign('role_id')
                  ->references('id')
                  ->on('roles')
                  ->nullOnDelete(); // If role deleted, user loses access (fail-closed)
        });

        // Drop old flat `role` enum column — replaced by role_id FK
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });

        // -------------------------------------------------------
        // orders table: make user_id nullable for guest checkout
        // -------------------------------------------------------
        Schema::table('orders', function (Blueprint $table) {
            // Drop the existing restrictOnDelete FK before modifying column
            $table->dropForeign(['user_id']);

            // Make nullable — guest orders have no authenticated user
            $table->unsignedBigInteger('user_id')
                  ->nullable()
                  ->comment('NULL for guest orders. Set when a logged-in user orders.')
                  ->change();

            // Re-add FK with nullOnDelete (soft safety)
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        // Restore orders.user_id to NOT NULL
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->unsignedBigInteger('user_id')
                  ->nullable(false)
                  ->change();
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->restrictOnDelete();
        });

        // Restore users table
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['customer', 'cashier', 'admin'])
                  ->default('customer')
                  ->index()
                  ->after('password');

            $table->dropForeign(['role_id']);
            $table->dropColumn(['username', 'role_id', 'expired_at']);
        });
    }
};
