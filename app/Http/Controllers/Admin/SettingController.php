<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{

    public function storeIdentity()
    {
        $storeName    = Setting::getValue('store_name', 'Kumaw X Atmosphr');
        $storeAddress = Setting::getValue('store_address', 'Jl. Contoh Alamat No. 123');
        $storePhone   = Setting::getValue('store_phone', '081234567890');

        return view('admin.settings.store_identity', compact('storeName', 'storeAddress', 'storePhone'));
    }

    public function updateStoreIdentity(Request $request)
    {
        $request->validate([
            'store_name'    => ['required', 'string', 'max:255'],
            'store_address' => ['required', 'string', 'max:500'],
            'store_phone'   => ['required', 'string', 'max:50'],
        ]);

        Setting::updateOrCreate(['key' => 'store_name'], ['value' => $request->store_name, 'type' => 'string']);
        Setting::updateOrCreate(['key' => 'store_address'], ['value' => $request->store_address, 'type' => 'string']);
        Setting::updateOrCreate(['key' => 'store_phone'], ['value' => $request->store_phone, 'type' => 'string']);

        return back()->with('success', 'Identitas toko berhasil diperbarui!');
    }

    public function qrCodes(Request $request)
    {
        $tableCount = $request->input('tables', 10); // Default to 10 tables if not provided
        return view('admin.settings.qr-codes', compact('tableCount'));
    }
}
