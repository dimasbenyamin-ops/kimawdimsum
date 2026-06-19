<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: create_menu_master_recipes_table
 *
 * Pivot between Menu and Master Recipes.
 * Stores the multiplier. Example: Menu "Dimsum 4 pcs" -> 4x "Dimsum Original (Per Biji)"
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('menu_master_recipes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('menu_id')
                  ->constrained('menus')
                  ->cascadeOnDelete();

            $table->foreignId('master_recipe_id')
                  ->constrained('master_recipes')
                  ->restrictOnDelete();

            $table->decimal('multiplier', 15, 4)
                  ->default(1)
                  ->comment('Multiplier, e.g. 4 for 4 pieces');

            $table->timestamps();

            $table->unique(['menu_id', 'master_recipe_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menu_master_recipes');
    }
};
