<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * Adds 'pending_payment' to the status ENUM on the orders table.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->enum('status', [
                'pending_payment',
                'pending',
                'confirmed',
                'preparing',
                'ready',
                'completed',
                'cancelled'
            ])->default('pending_payment')->change();
        });
    }

    public function down(): void
    {
        // Kembalikan semua order dengan status 'pending_payment' menjadi 'pending'
        // untuk mencegah error Data Truncation/Invalid Enum saat kolom status di-rollback.
        DB::table('orders')->where('status', 'pending_payment')->update(['status' => 'pending']);

        Schema::table('orders', function (Blueprint $table) {
            $table->enum('status', [
                'pending',
                'confirmed',
                'preparing',
                'ready',
                'completed',
                'cancelled'
            ])->default('pending')->change();
        });
    }
};
