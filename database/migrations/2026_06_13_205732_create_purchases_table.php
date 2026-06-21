<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: create_purchases_table
 *
 * Records stock purchase transactions from suppliers.
 * Depends on the `suppliers` table — ensure
 * 2026_06_13_205700_create_suppliers_table runs first.
 *
 * Purchase Status Flow:
 *   pending → received → cancelled
 *
 * `total_amount` is the grand total paid to the supplier for
 * this purchase order (sum of all line items, stored denormalized
 * for quick reporting).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();

            // Reference number for this purchase order
            $table->string('reference_number', 50)
                  ->unique()
                  ->nullable()
                  ->comment('e.g. PO-260613-0001 — human-readable purchase order code');

            // Supplier this purchase was made from
            $table->foreignId('supplier_id')
                  ->constrained('suppliers')
                  ->restrictOnDelete()
                  ->comment('The supplier who fulfilled this purchase order');

            // Staff member who recorded the purchase
            $table->foreignId('recorded_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete()
                  ->comment('Admin or staff who entered this purchase record');

            // Purchase lifecycle status
            $table->enum('status', ['pending', 'received', 'cancelled'])
                  ->default('pending')
                  ->index()
                  ->comment('Current state of the purchase order');

            // Financial summary
            $table->decimal('total_amount', 12, 2)
                  ->default(0)
                  ->comment('Grand total paid to supplier in IDR');

            // Dates
            $table->date('purchased_at')
                  ->nullable()
                  ->comment('Date the purchase was made or goods were ordered');

            $table->date('received_at')
                  ->nullable()
                  ->comment('Date the goods were physically received');

            $table->text('notes')
                  ->nullable()
                  ->comment('Internal notes about this purchase');

            $table->timestamps();
            $table->softDeletes()->comment('Preserve purchase history for auditing');

            // Composite indexes for reporting queries
            $table->index(['supplier_id', 'status']);
            $table->index(['status', 'purchased_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};
