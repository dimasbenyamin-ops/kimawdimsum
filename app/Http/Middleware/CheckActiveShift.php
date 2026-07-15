<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Shift;
use Illuminate\Support\Facades\Auth;

class CheckActiveShift
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if ($user) {
            // Block admin from accessing cashier modules completely
            if ($user->role && strtolower($user->role->name) === 'administrator') {
                return redirect()->route('admin.dashboard')
                    ->with('error', 'Administrator tidak diizinkan untuk mengakses halaman transaksi kasir.');
            }

            $activeShift = Shift::where('user_id', $user->id)
                ->where('status', 'open')
                ->first();

            if (!$activeShift) {
                // If they are not already trying to create a shift
                if (!$request->routeIs('cashier.shifts.*')) {
                    return redirect()->route('cashier.shifts.create')
                        ->with('warning', 'Anda harus membuka shift terlebih dahulu sebelum dapat mengakses halaman kasir.');
                }
            }
        }

        return $next($request);
    }
}
