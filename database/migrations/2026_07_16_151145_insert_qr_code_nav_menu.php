<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use App\Models\NavMenu;
use App\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $setupMenu = NavMenu::where('route_name', 'admin.setup')->first();
        $parentId = $setupMenu ? $setupMenu->id : null;

        $qrNav = NavMenu::updateOrCreate(
            ['route_name' => 'admin.settings.qr_codes'],
            [
                'name' => 'Cetak QR Meja',
                'icon' => 'bi bi-qr-code',
                'sort_order' => 15,
                'parent_id' => $parentId
            ]
        );

        $adminRole = Role::where('name', 'Administrator')->first();
        if ($adminRole) {
            $adminRole->navMenus()->syncWithoutDetaching([$qrNav->id]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $qrNav = NavMenu::where('route_name', 'admin.settings.qr_codes')->first();
        
        if ($qrNav) {
            $adminRole = Role::where('name', 'Administrator')->first();
            if ($adminRole) {
                $adminRole->navMenus()->detach($qrNav->id);
            }
            $qrNav->delete();
        }
    }
};
