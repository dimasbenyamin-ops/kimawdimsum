<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\Ingredient;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Purchase;
use App\Models\WasteLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    private function getDateRange(Request $request): array
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->toDateString());

        return [$startDate, $endDate];
    }

    public function profitLoss(Request $request): View
    {
        [$startDate, $endDate] = $this->getDateRange($request);

        // 1. Revenue
        $revenue = Order::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                        ->where('status', 'confirmed')
                        ->sum('total_amount');

        // 2. COGS (Cost of Goods Sold)
        // Since we don't store COGS snapshot per order item yet, we estimate it:
        // By looking at the sold items and their current average cost.
        $soldItems = OrderItem::whereHas('order', function ($q) use ($startDate, $endDate) {
                                  $q->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                                    ->where('status', 'confirmed');
                              })
                              ->get();
                              
        $cogs = 0;
        foreach ($soldItems as $item) {
            // Find recipes for this menu
            $recipes = DB::table('menu_master_recipes')
                         ->where('menu_id', $item->menu_id)
                         ->get();
                         
            foreach ($recipes as $recipe) {
                $multiplier = $recipe->multiplier;
                $ingredients = DB::table('master_recipe_items')
                                 ->join('ingredients', 'master_recipe_items.ingredient_id', '=', 'ingredients.id')
                                 ->where('master_recipe_items.master_recipe_id', $recipe->master_recipe_id)
                                 ->select('master_recipe_items.quantity', 'ingredients.avg_cost')
                                 ->get();
                                 
                foreach ($ingredients as $ing) {
                    $cogs += $item->quantity * $multiplier * $ing->quantity * $ing->avg_cost;
                }
            }
        }

        $grossProfit = $revenue - $cogs;

        // 3. Operational Expenses
        $expenses = Expense::whereBetween('expense_date', [$startDate, $endDate])->sum('amount');
        
        // 4. Waste Cost
        $wasteCost = WasteLog::whereBetween('waste_date', [$startDate, $endDate])->sum('cost_amount');

        $netProfit = $grossProfit - $expenses - $wasteCost;

        return view('admin.reports.profit-loss', compact(
            'startDate', 'endDate', 'revenue', 'cogs', 'grossProfit', 'expenses', 'wasteCost', 'netProfit'
        ));
    }

    public function cogs(Request $request): View
    {
        // Cost of goods sold per menu
        $menus = \App\Models\Menu::orderBy('name')->get();
        $menuCogs = [];

        foreach ($menus as $menu) {
            $cost = 0;
            $recipes = DB::table('menu_master_recipes')
                         ->where('menu_id', $menu->id)
                         ->get();
                         
            foreach ($recipes as $recipe) {
                $multiplier = $recipe->multiplier;
                $ingredients = DB::table('master_recipe_items')
                                 ->join('ingredients', 'master_recipe_items.ingredient_id', '=', 'ingredients.id')
                                 ->where('master_recipe_items.master_recipe_id', $recipe->master_recipe_id)
                                 ->select('master_recipe_items.quantity', 'ingredients.avg_cost')
                                 ->get();
                                 
                foreach ($ingredients as $ing) {
                    $cost += $multiplier * $ing->quantity * $ing->avg_cost;
                }
            }

            $margin = $menu->price > 0 ? (($menu->price - $cost) / $menu->price) * 100 : 0;

            $menuCogs[] = [
                'name' => $menu->name,
                'price' => $menu->price,
                'cost' => $cost,
                'profit' => $menu->price - $cost,
                'margin' => round($margin, 2),
            ];
        }

        return view('admin.reports.cogs', compact('menuCogs'));
    }

    public function stockAudit(Request $request): View
    {
        $ingredients = Ingredient::with('unit')->orderBy('name')->get();
        $totalStockValue = 0;
        foreach ($ingredients as $ing) {
            $totalStockValue += ($ing->current_stock * $ing->avg_cost);
        }

        return view('admin.reports.stock-audit', compact('ingredients', 'totalStockValue'));
    }

    public function salesPerMenu(Request $request): View
    {
        [$startDate, $endDate] = $this->getDateRange($request);

        $sales = OrderItem::select('menu_id', DB::raw('SUM(quantity) as total_qty'), DB::raw('SUM(subtotal) as total_revenue'))
                          ->whereHas('order', function ($q) use ($startDate, $endDate) {
                              $q->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                                ->where('status', 'confirmed');
                          })
                          ->groupBy('menu_id')
                          ->with('menu')
                          ->get()
                          ->sortByDesc('total_qty');

        return view('admin.reports.sales-per-menu', compact('sales', 'startDate', 'endDate'));
    }

    public function destroySalesPerMenu(Request $request, $menuId)
    {
        [$startDate, $endDate] = $this->getDateRange($request);

        // Find all order items for this menu within the date range
        $orderItems = OrderItem::where('menu_id', $menuId)
            ->whereHas('order', function ($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
            })->get();

        if ($orderItems->isEmpty()) {
            return back()->with('error', 'Tidak ada data penjualan untuk menu ini pada periode yang dipilih.');
        }

        $orderIds = $orderItems->pluck('order_id')->unique();

        // Delete the order items
        OrderItem::whereIn('id', $orderItems->pluck('id'))->delete();

        // Check the affected orders and either recalculate their totals or delete them if empty
        foreach ($orderIds as $orderId) {
            $order = Order::find($orderId);
            if ($order) {
                if ($order->items()->count() === 0) {
                    $order->delete();
                } else {
                    $newTotal = $order->items()->sum('subtotal');
                    $order->update(['total_amount' => $newTotal]);
                }
            }
        }

        return back()->with('success', 'Data penjualan testing untuk menu tersebut berhasil dihapus dari database.');
    }

    public function cashFlow(Request $request): View
    {
        [$startDate, $endDate] = $this->getDateRange($request);

        // Inflow (Revenue)
        $inflow = Order::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                        ->where('status', 'confirmed')
                        ->sum('total_amount');

        // Outflow (Purchases Confirmed)
        $purchases = Purchase::whereBetween('purchase_date', [$startDate, $endDate])
                             ->where('status', 'confirmed')
                             ->sum('total_amount');

        // Outflow (Expenses)
        $expenses = Expense::whereBetween('expense_date', [$startDate, $endDate])->sum('amount');

        $totalOutflow = $purchases + $expenses;
        $netCashFlow = $inflow - $totalOutflow;

        return view('admin.reports.cash-flow', compact(
            'startDate', 'endDate', 'inflow', 'purchases', 'expenses', 'totalOutflow', 'netCashFlow'
        ));
    }
}
