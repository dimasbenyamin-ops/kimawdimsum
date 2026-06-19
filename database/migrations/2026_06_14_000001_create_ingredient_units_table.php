<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: create_ingredient_units_table
 *
 * Stores measurement units for ingredients (gram, kg, liter, ml, pcs, etc.).
 * Supports unit conversion via base_unit_id + conversion_factor.
 * Example: kg (base=null), g (base=kg, factor=0.001)
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ingredient_units', function (Blueprint $table) {
            $table->id();

            $table->string('name', 50)
                  ->comment('Full unit name, e.g. "kilogram"');

            $table->string('abbreviation', 10)
                  ->unique()
                  ->comment('Short form, e.g. "kg", "g", "pcs"');

            // Self-referential FK for unit conversion
            $table->unsignedBigInteger('base_unit_id')
                  ->nullable()
                  ->comment('Reference to the base unit for conversion (null = is base unit)');

            $table->decimal('conversion_factor', 15, 6)
                  ->default(1)
                  ->comment('Multiplier to convert TO base unit. e.g. 1 kg = 1000 g → factor = 1000');

            $table->timestamps();

            $table->foreign('base_unit_id')
                  ->references('id')
                  ->on('ingredient_units')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ingredient_units');
    }
};
