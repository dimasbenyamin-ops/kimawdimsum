<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\WhatsAppNotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Valid status state machine transitions.
     * Cashiers can only move forward; cannot skip states.
     */
    private const VALID_TRANSITIONS = [
        Order::STATUS_PENDING_PAYMENT => [Order::STATUS_CONFIRMED, Order::STATUS_CANCELLED],
        Order::STATUS_PENDING   => [Order::STATUS_CONFIRMED, Order::STATUS_CANCELLED],
        Order::STATUS_CONFIRMED => [Order::STATUS_PREPARING, Order::STATUS_CANCELLED],
        Order::STATUS_PREPARING => [Order::STATUS_READY],
        Order::STATUS_READY     => [Order::STATUS_COMPLETED],
    ];

    public function index(): View
    {
        $activeOrders = Order::with(['customer', 'items'])
            ->active()
            ->today()
            ->orderBy('created_at')
            ->get()
            ->groupBy('status');

        $statusOrder = [
            Order::STATUS_PENDING_PAYMENT,
            Order::STATUS_CONFIRMED,
            Order::STATUS_PREPARING,
            Order::STATUS_READY,
        ];

        $todayCompleted = Order::today()->byStatus(Order::STATUS_COMPLETED)->count();
        $todayRevenue   = Order::today()->byStatus(Order::STATUS_COMPLETED)->sum('total_amount');

        return view('cashier.dashboard', compact('activeOrders', 'statusOrder', 'todayCompleted', 'todayRevenue'));
    }

    public function updateStatus(Request $request, Order $order, WhatsAppNotificationService $whatsapp): RedirectResponse
    {
        $validated = $request->validate([
            'status'         => ['required', 'string', 'in:confirmed,preparing,ready,completed,cancelled'],
            'cashier_notes'  => ['nullable', 'string', 'max:500'],
            'payment_method' => ['nullable', 'in:cash,qris'],
        ]);

        $newStatus     = $validated['status'];
        $currentStatus = $order->status;

        // Enforce state machine — deny invalid transitions
        $allowed = self::VALID_TRANSITIONS[$currentStatus] ?? [];
        if (! in_array($newStatus, $allowed, true)) {
            return back()->withErrors(['status' => 'Perubahan status tidak valid untuk pesanan ini.']);
        }

        $updateData = [
            'status'       => $newStatus,
            'processed_by' => Auth::id(),
        ];

        if (! empty($validated['cashier_notes'])) {
            $updateData['cashier_notes'] = $validated['cashier_notes'];
        }

        // Handle specific logic when confirming or completing
        if ($newStatus === Order::STATUS_CONFIRMED && in_array($currentStatus, [Order::STATUS_PENDING, Order::STATUS_PENDING_PAYMENT])) {
            // Set estimated time: 15 minutes base + 2 minutes per item
            $order->loadMissing('items');
            $itemCount = $order->items->sum('quantity'); // Or just count() if you want per unique item, but quantity makes more sense
            $minutes = 15 + ($itemCount * 2);
            $updateData['estimated_ready_at'] = now()->addMinutes($minutes);

            if ($currentStatus === Order::STATUS_PENDING_PAYMENT) {
                $updateData['paid_at'] = now(); // Cashier confirmed payment is received
            }
        }
        
        if ($newStatus === Order::STATUS_COMPLETED && !$order->isPaid()) {
            $updateData['paid_at'] = now();
        }
        if (!empty($validated['payment_method'])) {
            $updateData['payment_method'] = $validated['payment_method'];
        }

        $order->update($updateData);

        // Trigger Notification
        if ($newStatus === Order::STATUS_READY) {
            $whatsapp->sendOrderReadyNotification($order);
        }

        return back()->with('success', 'Status pesanan #' . e($order->order_number) . ' diperbarui menjadi ' . e($order->statusLabel) . '.');
    }
}
