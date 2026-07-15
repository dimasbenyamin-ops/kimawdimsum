<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: create_ingredients_table
 *
 * Master data for raw ingredients / bahan baku.
 * Tracks current stock level and weighted average cost.
 * Soft-deleted to preserve historical references in stock movements.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ingredients', function (Blueprint $table) {
            $table->id();

            $table->string('name', 100)
                  ->comment('Ingredient name, e.g. "Tepung Terigu", "Udang"');

            $table->string('sku', 50)
                  ->nullable()
                  ->unique()
                  ->comment('Internal SKU code');

            $table->foreignId('unit_id')
                  ->constrained('ingredient_units')
                  ->restrictOnDelete()
                  ->comment('Storage / base unit for this ingredient');

            $table->string('category', 50)
                  ->default('lainnya')
                  ->index()
                  ->comment('bumbu, protein, sayur, tepung, kemasan, lainnya');

            $table->decimal('min_stock', 15, 4)
                  ->default(0)
                  ->comment('Minimum stock threshold for low-stock alert');

            $table->decimal('current_stock', 15, 4)
                  ->default(0)
                  ->comment('Current stock quantity (updated by triggers/events)');

            $table->decimal('avg_cost', 15, 4)
                  ->default(0)
                  ->comment('Weighted Average Cost per unit');

            $table->boolean('is_active')
                  ->default(true)
                  ->index()
                  ->comment('Toggle visibility without deletion');

            $table->timestamps();
            $table->softDeletes();

            $table->index(['category', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ingredients');
    }
};
