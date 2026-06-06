<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Adds 'pending_payment' to the status ENUM on the orders table.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE `orders` MODIFY `status` ENUM(
            'pending_payment',
            'pending',
            'confirmed',
            'preparing',
            'ready',
            'completed',
            'cancelled'
        ) NOT NULL DEFAULT 'pending_payment'");
    }

    public function down(): void
    {
        DB::statement("UPDATE `orders` SET `status` = 'pending' WHERE `status` = 'pending_payment'");
        DB::statement("ALTER TABLE `orders` MODIFY `status` ENUM(
            'pending',
            'confirmed',
            'preparing',
            'ready',
            'completed',
            'cancelled'
        ) NOT NULL DEFAULT 'pending'");
    }
};
