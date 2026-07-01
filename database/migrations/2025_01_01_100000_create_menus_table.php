<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: create_menus_table
 *
 * Stores all dimsum menu items managed by admin.
 * Supports categories (dim sum types), availability toggle,
 * pricing, and soft deletes (never lose historical menu data
 * referenced by old order_items).
 *
 * Categories: siomay, hakau, lumpia, bao, shumai, minuman, lainnya
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('menus', function (Blueprint $table) {
            $table->id();

            $table->string('name')->comment('Menu item name, e.g. "Siomay Udang"');
            $table->string('slug')->unique()->comment('URL-friendly identifier');

            $table->text('description')->nullable()->comment('Detailed menu description');

            $table->string('category')->default('lainnya')->index()->comment('Dimsum category');

            $table->decimal('price', 10, 2)->comment('Price in IDR (e.g., 15000.00)');

            $table->string('image_path')->nullable()->comment('Relative path to menu image in storage');

            $table->boolean('is_available')
                  ->default(true)
                  ->index()
                  ->comment('Toggle item availability without deleting');

            $table->integer('sort_order')
                  ->default(0)
                  ->comment('Display order on menu page');

            $table->unsignedBigInteger('created_by')
                  ->nullable()
                  ->comment('Admin user who created the item');

            $table->timestamps();
            $table->softDeletes()->comment('Soft delete preserves data for existing order_items');

            // Foreign key (loose — admin may be deleted)
            $table->foreign('created_by')
                  ->references('id')
                  ->on('users')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menus');
    }
};
