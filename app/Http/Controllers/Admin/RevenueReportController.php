<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Carbon\Carbon;

class RevenueReportController extends Controller
{
    public function index(Request $request): View
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $query = Order::query()->where('status', Order::STATUS_COMPLETED);

        if ($startDate && $endDate) {
            $end = Carbon::parse($endDate)->endOfDay();
            $query->whereBetween('paid_at', [$startDate, $end]);
        } else {
            // Default to current month
            $startDate = Carbon::now()->startOfMonth()->toDateString();
            $endDate = Carbon::now()->endOfMonth()->toDateString();
            $query->whereBetween('paid_at', [
                Carbon::now()->startOfMonth(), 
                Carbon::now()->endOfMonth()
            ]);
        }

        $orders = $query->orderBy('paid_at', 'desc')->get();

        $totalRevenue = $orders->sum('total_amount');
        $totalOrders = $orders->count();
        $averageOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;

        return view('admin.reports.revenue', compact(
            'orders', 
            'totalRevenue', 
            'totalOrders', 
            'averageOrderValue',
            'startDate',
            'endDate'
        ));
    }
}
