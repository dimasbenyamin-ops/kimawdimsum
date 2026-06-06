<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

/**
 * AdminDashboardController
 *
 * Entry point for authenticated staff after login.
 * All roles land here; further navigation is limited by their role_menu assignments.
 */
class AdminDashboardController extends Controller
{
    public function index(): View
    {
        return view('admin.dashboard');
    }
}
