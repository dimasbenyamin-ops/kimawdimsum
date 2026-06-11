<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('menus', function (Blueprint $table) {
            $table->string('badge')->nullable()->after('is_available')->comment('e.g., Best Seller, Recommended');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->timestamp('estimated_ready_at')->nullable()->after('paid_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('menus', function (Blueprint $table) {
            $table->dropColumn('badge');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('estimated_ready_at');
        });
    }
};
