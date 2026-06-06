<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: create_order_items_table
 *
 * Line items belonging to an order. Each row represents one
 * menu item in a specific quantity.
 *
 * IMPORTANT DESIGN DECISION — Price Snapshot:
 * `unit_price` copies menu.price at order time.
 * This preserves the exact amount charged even if menu prices
 * change later. Never join to menus.price for financial data;
 * always use order_items.unit_price.
 *
 * `menu_id` is nullable (SET NULL on delete) so that if a menu
 * item is hard-deleted, the order item record survives with a
 * snapshot of what was ordered (name stored separately).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();

            // Parent order
            $table->foreignId('order_id')
                  ->constrained('orders')
                  ->cascadeOnDelete()
                  ->comment('The order this item belongs to');

            // Reference to menu (nullable to survive menu deletion)
            $table->foreignId('menu_id')
                  ->nullable()
                  ->constrained('menus')
                  ->nullOnDelete()
                  ->comment('Menu item ordered (nullable if menu is deleted)');

            // Snapshot fields — captured at order time
            $table->string('menu_name')
                  ->comment('Snapshot of menu.name at order time');

            $table->string('menu_category')
                  ->nullable()
                  ->comment('Snapshot of menu.category at order time');

            // Quantity and pricing
            $table->unsignedSmallInteger('quantity')
                  ->default(1)
                  ->comment('Number of this item ordered');

            $table->decimal('unit_price', 10, 2)
                  ->comment('Price per unit at the time of order (snapshot)');

            $table->decimal('subtotal', 10, 2)
                  ->storedAs('quantity * unit_price')
                  ->comment('Computed: quantity × unit_price (stored generated column)');

            // Per-item special instructions
            $table->string('notes', 500)
                  ->nullable()
                  ->comment('e.g., "extra pedas", "tanpa bawang"');

            $table->timestamps();

            // Index for fast order summary queries
            $table->index(['order_id', 'menu_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
