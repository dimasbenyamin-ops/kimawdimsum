<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: create_suppliers_table
 *
 * Master data for ingredient suppliers.
 * Linked to purchases for tracking who supplied what.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();

            $table->string('name', 100)
                  ->comment('Supplier / vendor name');

            $table->string('contact_person', 100)
                  ->nullable()
                  ->comment('PIC / person in charge');

            $table->string('phone', 30)
                  ->nullable()
                  ->comment('Phone number');

            $table->text('address')
                  ->nullable()
                  ->comment('Full address');

            $table->text('notes')
                  ->nullable()
                  ->comment('Internal notes');

            $table->boolean('is_active')
                  ->default(true)
                  ->index()
                  ->comment('Soft toggle to hide inactive suppliers');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
