<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Shift;
use Illuminate\Support\Facades\Auth;

class ShiftController extends Controller
{
    public function create()
    {
        // If already has an open shift, redirect to dashboard
        $activeShift = Shift::where('user_id', Auth::id())->where('status', 'open')->first();
        if ($activeShift) {
            return redirect()->route('cashier.dashboard');
        }

        return view('cashier.shifts.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'initial_cash' => 'required|numeric|min:0',
        ]);

        // Check if THIS user already has an open shift
        $activeShift = Shift::where('user_id', Auth::id())->where('status', 'open')->first();
        if ($activeShift) {
            return redirect()->route('cashier.dashboard')->with('error', 'Anda sudah memiliki shift yang aktif.');
        }

        // Cek apakah ada shift apa pun yang masih berstatus 'open' (milik siapa pun)
        $activeShift = Shift::where('status', 'open')->exists();

        if ($activeShift) {
            return back()->with('error', 'Gagal! Masih ada shift yang belum ditutup. Silakan selesaikan closing shift sebelumnya terlebih dahulu.');
        }

        Shift::create([
            'user_id' => Auth::id(),
            'start_time' => now(),
            'initial_cash' => $request->initial_cash,
            'status' => 'open',
        ]);

        return redirect()->route('cashier.dashboard')->with('success', 'Shift berhasil dimulai!');
    }

    public function summary()
    {
        $activeShift = Shift::where('user_id', Auth::id())->where('status', 'open')->with('orders')->first();
        
        if (!$activeShift) {
            return redirect()->route('cashier.shifts.create')->with('error', 'Tidak ada shift aktif untuk ditutup.');
        }

        $cashTotal = $activeShift->orders->where('payment_method', 'cash')->sum('total_amount');
        $qrisTotal = $activeShift->orders->where('payment_method', 'qris')->sum('total_amount');
        $expectedCash = $activeShift->initial_cash + $cashTotal;

        return view('cashier.shifts.end', compact('activeShift', 'cashTotal', 'qrisTotal', 'expectedCash'));
    }

    public function close(Request $request)
    {
        $request->validate([
            'final_cash' => 'required|numeric|min:0',
        ]);

        $activeShift = Shift::where('user_id', Auth::id())->where('status', 'open')->first();
        if (!$activeShift) {
            return redirect()->route('cashier.dashboard')->with('error', 'Tidak ada shift aktif.');
        }

        $activeShift->update([
            'end_time' => now(),
            'final_cash' => $request->final_cash,
            'status' => 'closed',
        ]);

        // You might want to log the user out after closing the shift, or just redirect to dashboard
        return redirect()->route('cashier.dashboard')->with('success', 'Shift berhasil ditutup.');
    }
}
