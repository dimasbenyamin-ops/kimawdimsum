<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\MenuAdminController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\RoleMenuController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Customer\CartController;
use App\Http\Controllers\Customer\MenuController;
use App\Http\Controllers\Customer\OrderController;
use App\Http\Controllers\Cashier\DashboardController;
use App\Http\Controllers\Cashier\OrderController as CashierOrderController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// ── Root: redirect to customer menu ─────────────────────────────────────────
Route::get('/', fn () => redirect()->route('menu.index'));

// ============================================================
// STAFF AUTH — Login / Logout only (no public registration)
// ============================================================
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])
         ->name('login.store')
         ->middleware('throttle:10,1'); // 10 attempts per minute per IP
});

Route::post('/logout', [LoginController::class, 'destroy'])
     ->name('logout')
     ->middleware('auth');

// ============================================================
// PUBLIC — Customer / Guest Checkout (NO login required)
// Customers browse the menu, add to cart (session-based),
// and place orders as unauthenticated guests.
// ============================================================
Route::get('/menu', [MenuController::class, 'index'])->name('menu.index');

// Cart — session-based, no auth required
Route::prefix('cart')->name('cart.')->group(function () {
    Route::get('/',          [CartController::class, 'index'])->name('index');
    Route::post('/add',      [CartController::class, 'add'])->name('add');
    Route::delete('/{menuId}', [CartController::class, 'remove'])->name('remove');
    Route::post('/clear',    [CartController::class, 'clear'])->name('clear');
});

// Orders — guest checkout (user_id nullable on orders table)
Route::prefix('orders')->name('orders.')->group(function () {
    Route::get('/',        [OrderController::class, 'index'])->name('index');
    Route::post('/',       [OrderController::class, 'store'])->name('store');
    Route::get('/{order}', [OrderController::class, 'show'])->name('show');
});

// ============================================================
// BACKEND — Admin & Cashier Dashboard
// All routes require:
//   1. Authentication (auth)
//   2. Non-expired account
//   3. Dynamic role_menu permission (admin.access middleware)
// ============================================================
Route::middleware(['auth', 'admin.access'])
     ->prefix('admin')
     ->name('admin.')
     ->group(function () {

         // ── Profile ────────────────────────────────────────────────────────
         Route::post('/password/update', [ProfileController::class, 'updatePassword'])->name('password.update');

         // ── Dashboard (open to all authenticated, non-expired staff) ──────────
         Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

         // ── Setup > User Management ───────────────────────────────────────────
         Route::resource('users', UserController::class);

         // ── Setup > Role ──────────────────────────────────────────────────────
         Route::resource('roles', RoleController::class);

         // ── Setup > Role Menu ─────────────────────────────────────────────────
         Route::get('/role-menu/{role}',  [RoleMenuController::class, 'index'])->name('role-menu.index');
         Route::post('/role-menu/{role}', [RoleMenuController::class, 'sync'])->name('role-menu.sync');

         // ── Setup > QRIS ──────────────────────────────────────────────────────
         Route::get('/settings/qris',  [SettingController::class, 'qris'])->name('settings.qris');
         Route::post('/settings/qris', [SettingController::class, 'updateQris'])->name('settings.qris.update');

         // ── Reports ───────────────────────────────────────────────────────────
         Route::get('/reports/revenue', [App\Http\Controllers\Admin\RevenueReportController::class, 'index'])->name('reports.revenue');

         // ── Menu Management (food items) ──────────────────────────────────────
         Route::resource('menus', MenuAdminController::class);
     });

// ============================================================
// CASHIER DASHBOARD (legacy — kept for real-time order updates)
// Requires auth; permission checked via admin.access middleware.
// ============================================================
Route::middleware(['auth', 'admin.access'])
     ->prefix('cashier')
     ->name('cashier.')
     ->group(function () {
         Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
         Route::patch('/orders/{order}/status', [DashboardController::class, 'updateStatus'])
              ->name('orders.updateStatus');

         // ── Pesan di Kasir (cashier-initiated POS order) ──────────────────
         Route::get('/order/create', [CashierOrderController::class, 'create'])->name('order.create');
         Route::post('/order',       [CashierOrderController::class, 'store'])->name('order.store');
     });
