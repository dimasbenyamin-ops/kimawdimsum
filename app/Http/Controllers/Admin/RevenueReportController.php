<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RevenueReportController extends Controller
{
    public function index(Request $request)
    {
        // Redirect to the new profit & loss report
        return redirect()->route('admin.reports.profit-loss');
    }
}
