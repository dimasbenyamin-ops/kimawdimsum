<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: create_suppliers_table
 *
 * Stores supplier/vendor information for inventory and purchasing.
 * Must run BEFORE create_purchases_table because the purchases table
 * holds a foreign key referencing suppliers.id.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();

            $table->string('name')->comment('Supplier or vendor company name');

            $table->string('contact_person')
                  ->nullable()
                  ->comment('Primary contact name at the supplier');

            $table->string('phone', 30)
                  ->nullable()
                  ->comment('Contact phone number');

            $table->string('email')
                  ->nullable()
                  ->comment('Contact email address');

            $table->text('address')
                  ->nullable()
                  ->comment('Physical or mailing address');

            $table->text('notes')
                  ->nullable()
                  ->comment('Internal notes about this supplier');

            $table->boolean('is_active')
                  ->default(true)
                  ->index()
                  ->comment('Soft-disable a supplier without deleting records');

            $table->timestamps();
            $table->softDeletes()->comment('Preserve supplier history referenced by purchases');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
