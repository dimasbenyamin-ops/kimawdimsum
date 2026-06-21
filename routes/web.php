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
use App\Http\Controllers\Customer\ReviewController;
use App\Http\Controllers\Cashier\DashboardController;
use App\Http\Controllers\Cashier\OrderController as CashierOrderController;
use App\Http\Controllers\Cashier\ShiftController;
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
    Route::put('/{menuId}',  [CartController::class, 'update'])->name('update');
    Route::delete('/{menuId}', [CartController::class, 'remove'])->name('remove');
    Route::post('/clear',    [CartController::class, 'clear'])->name('clear');
    Route::post('/reorder/{order}', [CartController::class, 'reorder'])->name('reorder');
});

// Orders — guest checkout (user_id nullable on orders table)
Route::prefix('orders')->name('orders.')->group(function () {
    Route::get('/',        [OrderController::class, 'index'])->name('index');
    Route::post('/',       [OrderController::class, 'store'])->name('store');
    Route::get('/{order}', [OrderController::class, 'show'])->name('show');
    Route::get('/{order}/snap-token', [\App\Http\Controllers\PaymentController::class, 'getSnapToken'])->name('snap_token');
    Route::post('/check-status', [\App\Http\Controllers\PaymentController::class, 'checkStatus'])->name('check_status');
    Route::post('/{order}/review', [ReviewController::class, 'store'])->name('review.store');
});

// Chatbot routes
Route::prefix('chat')->name('chat.')->group(function () {
    Route::post('/', [\App\Http\Controllers\Customer\ChatbotController::class, 'chat'])->name('send');
    Route::post('/clear', [\App\Http\Controllers\Customer\ChatbotController::class, 'clearHistory'])->name('clear');
});

// Midtrans Webhook Notification Route
Route::post('/api/midtrans/notification', [\App\Http\Controllers\PaymentController::class, 'notification'])->name('midtrans.notification');

// DOKU Webhook Notification Route
Route::any('/api/doku/notification', [\App\Http\Controllers\PaymentController::class, 'dokuNotification'])->name('doku.notification');

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

         // ── Setup > Nav Menus ─────────────────────────────────────────────────
         Route::resource('nav-menus', \App\Http\Controllers\Admin\NavMenuController::class)->except(['show']);


         // ── Setup > Store Identity ────────────────────────────────────────────
         Route::get('/settings/store-identity',  [SettingController::class, 'storeIdentity'])->name('settings.store_identity');
         Route::post('/settings/store-identity', [SettingController::class, 'updateStoreIdentity'])->name('settings.store_identity.update');

         // ── Reports ───────────────────────────────────────────────────────────
         Route::get('/reports/revenue', [App\Http\Controllers\Admin\RevenueReportController::class, 'index'])->name('reports.revenue');

         // ── Menu Management (food items) ──────────────────────────────────────
         Route::post('menus/{menu}/recipe', [\App\Http\Controllers\Admin\MenuAdminController::class, 'syncRecipe'])->name('menus.syncRecipe');
         Route::resource('menus', \App\Http\Controllers\Admin\MenuAdminController::class);

         // ── Inventory / Back-Office ─────────────────────────────────────────────
         Route::resource('ingredients', \App\Http\Controllers\Admin\IngredientController::class)->except(['show']);
         Route::resource('ingredient-units', \App\Http\Controllers\Admin\IngredientUnitController::class)->except(['show']);
         Route::resource('suppliers', \App\Http\Controllers\Admin\SupplierController::class)->except(['show']);
         
         Route::resource('master-recipes', \App\Http\Controllers\Admin\MasterRecipeController::class)->except(['show']);
         Route::resource('assets', \App\Http\Controllers\Admin\AssetController::class)->except(['show']);

         // ── Purchasing & Waste ────────────────────────────────────────────────
         Route::patch('purchases/{purchase}/status', [\App\Http\Controllers\Admin\PurchaseController::class, 'updateStatus'])->name('purchases.status');
         Route::resource('purchases', \App\Http\Controllers\Admin\PurchaseController::class)->only(['index', 'create', 'store', 'show']);
         Route::resource('waste', \App\Http\Controllers\Admin\WasteLogController::class)->only(['index', 'create', 'store']);

         // ── Stock Opname & Expenses ───────────────────────────────────────────
         Route::patch('stock-opnames/{stockOpname}/status', [\App\Http\Controllers\Admin\StockOpnameController::class, 'updateStatus'])->name('stock-opnames.status');
         Route::resource('stock-opnames', \App\Http\Controllers\Admin\StockOpnameController::class)->only(['index', 'create', 'store', 'show']);
         Route::resource('expense-categories', \App\Http\Controllers\Admin\ExpenseCategoryController::class)->except(['show']);
         Route::resource('expenses', \App\Http\Controllers\Admin\ExpenseController::class);

         // ── Reports (Keuangan & Analisis) ─────────────────────────────────────
         Route::get('reports/profit-loss', [\App\Http\Controllers\Admin\ReportController::class, 'profitLoss'])->name('reports.profit-loss');
         Route::get('reports/cogs', [\App\Http\Controllers\Admin\ReportController::class, 'cogs'])->name('reports.cogs');
         Route::get('reports/stock-audit', [\App\Http\Controllers\Admin\ReportController::class, 'stockAudit'])->name('reports.stock-audit');
         Route::get('reports/sales-per-menu', [\App\Http\Controllers\Admin\ReportController::class, 'salesPerMenu'])->name('reports.sales-per-menu');
         Route::delete('reports/sales-per-menu/{menuId}', [\App\Http\Controllers\Admin\ReportController::class, 'destroySalesPerMenu'])->name('reports.sales-per-menu.destroy');
         Route::get('reports/cash-flow', [\App\Http\Controllers\Admin\ReportController::class, 'cashFlow'])->name('reports.cash-flow');
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
         Route::get('/orders/{order}/receipt', [CashierOrderController::class, 'receipt'])->name('orders.receipt');

         // ── Shift Management ──────────────────
         Route::get('/shifts/create', [ShiftController::class, 'create'])->name('shifts.create');
         Route::post('/shifts', [ShiftController::class, 'store'])->name('shifts.store');
         Route::get('/shifts/summary', [ShiftController::class, 'summary'])->name('shifts.summary');
         Route::post('/shifts/close', [ShiftController::class, 'close'])->name('shifts.close');
     });
