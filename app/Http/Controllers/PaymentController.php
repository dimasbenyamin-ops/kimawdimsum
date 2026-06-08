<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Midtrans\Config;
use Midtrans\Snap;

class PaymentController extends Controller
{
    public function __construct()
    {
        // Set configuration based on env
        Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        Config::$isProduction = env('MIDTRANS_IS_PRODUCTION', false);
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    public function getSnapToken(Order $order)
    {
        // Check if order is already paid or not pending payment
        if ($order->isPaid() || $order->status !== Order::STATUS_PENDING_PAYMENT) {
            return response()->json([
                'status' => 'error',
                'message' => 'Pesanan sudah dibayar atau tidak bisa dibayar.'
            ], 400);
        }

        // Check if order payment method is qris (optional, but good for validation)
        if ($order->payment_method !== 'qris') {
            return response()->json([
                'status' => 'error',
                'message' => 'Metode pembayaran bukan QRIS.'
            ], 400);
        }

        // Prepare item details
        $itemDetails = [];
        foreach ($order->items as $item) {
            $itemDetails[] = [
                'id'       => $item->menu_id,
                'price'    => (int) $item->unit_price,
                'quantity' => $item->quantity,
                'name'     => substr($item->menu_name, 0, 50),
            ];
        }

        // Add tax if exists
        if ($order->tax_amount > 0) {
            $itemDetails[] = [
                'id'       => 'TAX',
                'price'    => (int) $order->tax_amount,
                'quantity' => 1,
                'name'     => 'Pajak (11%)',
            ];
        }

        $customerDetails = [
            'first_name' => $order->customer_name,
            'phone'      => $order->phone_number,
        ];

        // Format transaction details
        $params = [
            'transaction_details' => [
                'order_id'     => $order->order_number . '-' . time(), // append time to avoid duplicate order_id on midtrans if retried
                'gross_amount' => (int) $order->total_amount,
            ],
            'customer_details' => $customerDetails,
            'item_details'     => $itemDetails,
            'enabled_payments' => ['other_qris', 'gopay', 'shopeepay'], // QRIS payment channels
        ];

        try {
            $snapToken = Snap::getSnapToken($params);
            return response()->json([
                'status'     => 'success',
                'snap_token' => $snapToken
            ]);
        } catch (\Throwable $e) {
            Log::error('Midtrans Snap Token Error: ' . $e->getMessage());
            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal mendapatkan token pembayaran: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle Midtrans Webhook Notification
     */
    public function notification(Request $request)
    {
        $payload = $request->getContent();
        $notification = json_decode($payload);

        if (!$notification) {
            return response()->json(['message' => 'Invalid JSON'], 400);
        }

        $orderId = $notification->order_id;
        $statusCode = $notification->status_code;
        $grossAmount = $notification->gross_amount;
        $serverKey = env('MIDTRANS_SERVER_KEY');
        $signatureKey = $notification->signature_key;

        // Security: Verify Signature
        $calculatedSignature = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);
        
        if ($calculatedSignature !== $signatureKey) {
            Log::warning('Midtrans Webhook: Invalid Signature', ['payload' => $notification]);
            return response()->json(['message' => 'Invalid Signature'], 403);
        }

        // Extract internal order_number from Midtrans order_id (e.g. KD-250601-0001-1718100000 -> KD-250601-0001)
        $parts = explode('-', $orderId);
        array_pop($parts); // remove timestamp
        $orderNumber = implode('-', $parts);

        $order = Order::where('order_number', $orderNumber)->first();

        if (!$order) {
            Log::error('Midtrans Webhook: Order not found', ['order_number' => $orderNumber]);
            return response()->json(['message' => 'Order Not Found'], 404);
        }

        $transactionStatus = $notification->transaction_status;
        $fraudStatus = $notification->fraud_status ?? null;

        Log::info("Midtrans Webhook: Order {$orderNumber} status changed to {$transactionStatus}");

        if ($transactionStatus == 'capture' || $transactionStatus == 'settlement') {
            if ($fraudStatus == 'challenge') {
                // Ignore challenge for now, or set status to pending
            } else {
                $order->status = Order::STATUS_CONFIRMED; // Mark as confirmed / Lunas
                if (!$order->paid_at) {
                    $order->paid_at = now();
                }
            }
        } else if ($transactionStatus == 'cancel' || $transactionStatus == 'deny' || $transactionStatus == 'expire') {
            $order->status = Order::STATUS_CANCELLED;
        } else if ($transactionStatus == 'pending') {
            $order->status = Order::STATUS_PENDING_PAYMENT;
        }

        $order->save();

        return response()->json(['message' => 'Notification handled']);
    }

    /**
     * Manual Check Status (Fallback if Webhook is not received or local testing)
     */
    public function checkStatus(Request $request)
    {
        $midtransOrderId = $request->order_id;
        if (!$midtransOrderId) {
            return response()->json(['error' => 'No order_id provided'], 400);
        }

        try {
            // Get status directly from Midtrans (Secure)
            $status = \Midtrans\Transaction::status($midtransOrderId);
            
            // Re-use logic to update order
            $parts = explode('-', $midtransOrderId);
            array_pop($parts); // remove timestamp
            $orderNumber = implode('-', $parts);

            $order = Order::where('order_number', $orderNumber)->first();
            if (!$order) {
                return response()->json(['error' => 'Order not found'], 404);
            }

            $transactionStatus = $status->transaction_status;
            
            if ($transactionStatus == 'capture' || $transactionStatus == 'settlement') {
                $order->status = Order::STATUS_CONFIRMED;
                if (!$order->paid_at) {
                    $order->paid_at = now();
                }
            } else if ($transactionStatus == 'cancel' || $transactionStatus == 'deny' || $transactionStatus == 'expire') {
                $order->status = Order::STATUS_CANCELLED;
            } else if ($transactionStatus == 'pending') {
                $order->status = Order::STATUS_PENDING_PAYMENT;
            }

            $order->save();

            return response()->json(['status' => 'success', 'order_status' => $order->status]);

        } catch (\Exception $e) {
            Log::error('Midtrans Check Status Error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
