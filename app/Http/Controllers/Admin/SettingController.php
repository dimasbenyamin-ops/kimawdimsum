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
}
