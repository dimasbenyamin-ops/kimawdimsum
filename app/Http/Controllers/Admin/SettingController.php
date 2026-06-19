<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function qris()
    {
        $qrisImage = Setting::getValue('qris_image');
        return view('admin.settings.qris', compact('qrisImage'));
    }

    public function updateQris(Request $request)
    {
        $request->validate([
            'qris_image' => ['required', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
        ]);

        $setting = Setting::firstOrCreate(['key' => 'qris_image'], ['type' => 'image']);

        // Delete old image if exists
        if ($setting->value && Storage::disk('public')->exists($setting->value)) {
            Storage::disk('public')->delete($setting->value);
        }

        $path = $request->file('qris_image')->store('qris', 'public');
        $setting->update(['value' => $path]);

        return back()->with('success', 'Gambar QRIS berhasil diperbarui!');
    }

    public function storeIdentity()
    {
        $storeName    = Setting::getValue('store_name', 'KUMAW DIMSUM');
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
}
