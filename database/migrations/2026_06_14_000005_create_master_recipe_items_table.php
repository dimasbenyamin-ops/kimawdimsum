<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: create_master_recipe_items_table
 *
 * Pivot between master recipes and ingredients.
 * Determines the BOM for one base unit of the master recipe.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('master_recipe_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('master_recipe_id')
                  ->constrained('master_recipes')
                  ->cascadeOnDelete();

            $table->foreignId('ingredient_id')
                  ->constrained('ingredients')
                  ->restrictOnDelete();

            $table->decimal('quantity', 15, 4)
                  ->comment('Amount of ingredient needed per master recipe unit');

            $table->timestamps();

            $table->unique(['master_recipe_id', 'ingredient_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('master_recipe_items');
    }
};
