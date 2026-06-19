<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: create_master_recipes_table
 *
 * Stores the base recipe / semi-finished products.
 * Example: "Dimsum Original (Per Biji)"
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('master_recipes', function (Blueprint $table) {
            $table->id();

            $table->string('name', 100)
                  ->comment('Name of the master recipe, e.g. "Dimsum Original (Per Biji)"');

            $table->string('description', 255)
                  ->nullable()
                  ->comment('Short description');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('master_recipes');
    }
};
