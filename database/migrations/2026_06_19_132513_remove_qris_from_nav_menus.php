<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Remove 'admin.settings.qris' from nav_menus table
        // This will also cascade and remove it from role_menu if foreign keys are set properly
        DB::table('nav_menus')->where('route_name', 'admin.settings.qris')->delete();
        
        // Also remove from settings table if exists
        DB::table('settings')->where('key', 'qris_image')->delete();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
