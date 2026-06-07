<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: create_orders_table
 *
 * Core transaction table. Each order belongs to a customer (user)
 * and tracks its full lifecycle through `status`.
 *
 * Order Status Flow:
 *   pending → confirmed → preparing → ready → completed
 *                    └──────────────────────→ cancelled
 *
 * Order Types:
 *   - dine_in   : Customer eats at restaurant
 *   - takeaway  : Customer picks up order
 *   - delivery  : Order delivered (future feature)
 *
 * The `order_number` is a human-readable unique code (e.g., KD-250601-0001)
 * used by cashiers for easy identification.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            // Human-readable order code for display
            $table->string('order_number', 30)
                  ->unique()
                  ->comment('e.g. KD-250601-0001 — generated in OrderObserver');

            // Relationship to customer
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->restrictOnDelete()
                  ->comment('The customer who placed this order');

            // Order lifecycle status
            $table->enum('status', [
                'pending',
                'confirmed',
                'preparing',
                'ready',
                'completed',
                'cancelled',
            ])->default('pending')
              ->index()
              ->comment('Lifecycle stage of the order');

            // Order type
            $table->enum('type', ['dine_in', 'takeaway', 'delivery'])
                  ->default('dine_in')
                  ->comment('How the customer will receive their order');

            // Financial summary (denormalized for quick reporting)
            $table->decimal('subtotal', 10, 2)
                  ->default(0)
                  ->comment('Sum of all order_items.subtotal');

            $table->decimal('discount_amount', 10, 2)
                  ->default(0)
                  ->comment('Discount in IDR');

            $table->decimal('tax_amount', 10, 2)
                  ->default(0)
                  ->comment('Tax (PPN) in IDR, usually 11% of subtotal');

            $table->decimal('total_amount', 10, 2)
                  ->default(0)
                  ->comment('subtotal - discount + tax');

            // Payment
            $table->enum('payment_method', ['cash', 'qris', 'unpaid'])
                  ->default('unpaid');

            $table->timestamp('paid_at')
                  ->nullable()
                  ->comment('When payment was confirmed by cashier');

            // Operational fields
            $table->unsignedSmallInteger('table_number')
                  ->nullable()
                  ->comment('Dine-in table number');

            $table->text('customer_notes')
                  ->nullable()
                  ->comment('Special requests from customer');

            $table->text('cashier_notes')
                  ->nullable()
                  ->comment('Internal notes added by cashier/admin');

            // Cashier who processed this order
            $table->foreignId('processed_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete()
                  ->comment('Cashier or admin who confirmed/processed');

            $table->timestamps();
            $table->softDeletes();

            // Composite index for dashboard queries
            $table->index(['status', 'created_at']);
            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
