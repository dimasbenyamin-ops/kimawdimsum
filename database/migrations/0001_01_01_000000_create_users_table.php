<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: create_users_table
 *
 * Extends the default Laravel users table with a `role` column
 * to differentiate between customer, cashier, and admin.
 *
 * Roles:
 *   - customer : Places orders via the frontend
 *   - cashier  : Manages the real-time order dashboard
 *   - admin    : Full access including menu CRUD and reports
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');

            // Role-based access control
            $table->enum('role', ['customer', 'cashier', 'admin'])
                  ->default('customer')
                  ->index()
                  ->comment('User role: customer | cashier | admin');

            $table->string('phone', 20)->nullable()->comment('WhatsApp-friendly phone number');
            $table->string('avatar')->nullable()->comment('Profile picture path');

            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes(); // Retain user data for order history
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};
