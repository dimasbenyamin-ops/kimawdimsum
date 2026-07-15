<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Order;
use App\Models\Review;

class ReviewController extends Controller
{
    public function store(Request $request, Order $order)
    {
        // Check if user owns the order or has it in session (guest)
        if (auth()->check()) {
            if ($order->user_id !== auth()->id()) {
                abort(403);
            }
        } else {
            $orderIds = session('order_ids', []);
            if (!in_array($order->id, $orderIds)) {
                abort(403);
            }
        }

        // Check if order is completed
        if (!$order->isCompleted()) {
            return back()->with('error', 'Review hanya bisa diberikan untuk pesanan yang sudah selesai.');
        }

        // Check if already reviewed
        if ($order->review()->exists()) {
            return back()->with('error', 'Anda sudah memberikan review untuk pesanan ini.');
        }

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $order->review()->create([
            'user_id' => auth()->id(),
            'rating' => $validated['rating'],
            'comment' => $validated['comment'] ?? null,
        ]);

        return back()->with('success', 'Terima kasih! Review Anda telah disimpan.');
    }
}
