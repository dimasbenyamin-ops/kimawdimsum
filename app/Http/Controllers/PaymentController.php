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
        Config::$serverKey = config('services.midtrans.server_key');
        Config::$isProduction = config('services.midtrans.is_production', false);
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    private function createDokuPayment(Order $order)
    {
        $clientId = config('services.doku.client_id');
        $secretKey = config('services.doku.secret_key');
        $isProduction = config('services.doku.is_production');

        $baseUrl = $isProduction ? 'https://api.doku.com' : 'https://api-sandbox.doku.com';
        $targetPath = '/checkout/v1/payment';
        $url = $baseUrl . $targetPath;

        $requestId = (string) \Illuminate\Support\Str::uuid();
        $timestamp = gmdate("Y-m-d\TH:i:s\Z");

        $invoiceNumber = $order->order_number . '-' . time();

        $body = [
            'order' => [
                'amount' => (int) $order->total_amount,
                'invoice_number' => $invoiceNumber,
                'currency' => 'IDR',
                'callback_url' => route('orders.show', $order->id), // Redirect back to order page
            ],
            'payment' => [
                'payment_due_date' => 60 // minutes
            ],
            'customer' => [
                'name' => $order->customer_name ?? 'Guest',
                'email' => 'customer@kumawdimsum.local', // placeholder if not exists
                'phone' => $order->phone_number ?? '081234567890',
            ]
        ];

        $jsonBody = json_encode($body);
        $digest = base64_encode(hash('sha256', $jsonBody, true));

        $componentSignature = "Client-Id:" . $clientId . "\n" .
                              "Request-Id:" . $requestId . "\n" .
                              "Request-Timestamp:" . $timestamp . "\n" .
                              "Request-Target:" . $targetPath . "\n" .
                              "Digest:" . $digest;

        $signature = base64_encode(hash_hmac('sha256', $componentSignature, $secretKey, true));
        $headerSignature = "HMACSHA256=" . $signature;

        $response = \Illuminate\Support\Facades\Http::withHeaders([
            'Client-Id' => $clientId,
            'Request-Id' => $requestId,
            'Request-Timestamp' => $timestamp,
            'Signature' => $headerSignature,
            'Content-Type' => 'application/json',
        ])->post($url, $body);

        if ($response->successful()) {
            return [
                'status' => 'success',
                'payment_url' => $response->json('response.payment.url'),
                'invoice_number' => $invoiceNumber
            ];
        }

        throw new \Exception('DOKU API Error: ' . $response->body());
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

        // 1. Try DOKU First (Active)
        try {
            $dokuResponse = $this->createDokuPayment($order);
            
            $order->payment_gateway = 'doku';
            $order->save();

            return response()->json([
                'status' => 'success',
                'gateway' => 'doku',
                'payment_url' => $dokuResponse['payment_url']
            ]);
        } catch (\Throwable $e) {
            Log::warning('DOKU Payment Failed, falling back to Midtrans. Error: ' . $e->getMessage());
        }

        // 2. Fallback to Midtrans (Passive)
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
            
            $order->payment_gateway = 'midtrans';
            $order->save();

            return response()->json([
                'status'     => 'success',
                'gateway'    => 'midtrans',
                'snap_token' => $snapToken
            ]);
        } catch (\Throwable $e) {
            Log::error('Midtrans Snap Token Error: ' . $e->getMessage());
            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal mendapatkan token pembayaran dari semua gateway: ' . $e->getMessage()
            ], 500);
        }
    }

    public function dokuNotification(Request $request)
    {
        $payload = $request->getContent();
        
        Log::info('DOKU Webhook Received', [
            'headers' => $request->headers->all(),
            'payload' => $payload
        ]);

        $clientId = $request->header('Client-Id');
        $requestId = $request->header('Request-Id');
        $timestamp = $request->header('Request-Timestamp');
        $signature = $request->header('Signature');

        // Jika DOKU mengirim request kosong (misalnya saat mengecek URL dari dashboard), langsung kembalikan 200 OK
        if (empty($payload) || empty($signature)) {
            return response()->json(['message' => 'Ping OK']);
        }

        $secretKey = config('services.doku.secret_key');

        
        $targetPath = $request->getPathInfo();

        $digest = base64_encode(hash('sha256', $payload, true));

        $componentSignature = "Client-Id:" . $clientId . "\n" .
                              "Request-Id:" . $requestId . "\n" .
                              "Request-Timestamp:" . $timestamp . "\n" .
                              "Request-Target:" . $targetPath . "\n" .
                              "Digest:" . $digest;

        $calculatedSignature = "HMACSHA256=" . base64_encode(hash_hmac('sha256', $componentSignature, $secretKey, true));

        // Security: Use hash_equals to prevent timing attacks
        if (!hash_equals($calculatedSignature, $signature)) {
            Log::warning('DOKU Webhook: Invalid Signature', ['payload' => $payload, 'calculated' => $calculatedSignature, 'signature' => $signature]);
            return response()->json(['message' => 'Invalid Signature'], 403);
        }

        $notification = json_decode($payload);

        $invoiceNumber = $notification->order->invoice_number ?? null;
        $transactionStatus = $notification->transaction->status ?? null;

        if (!$invoiceNumber) {
            return response()->json(['message' => 'No Invoice Number'], 400);
        }

        // Extract internal order_number from Doku invoice_number (e.g. KD-250601-0001-1718100000 -> KD-250601-0001)
        $parts = explode('-', $invoiceNumber);
        array_pop($parts); // remove timestamp
        $orderNumber = implode('-', $parts);

        $order = Order::where('order_number', $orderNumber)->first();

        if (!$order) {
            Log::error('DOKU Webhook: Order not found', ['order_number' => $orderNumber]);
            return response()->json(['message' => 'Order Not Found'], 404);
        }

        Log::info("DOKU Webhook: Order {$orderNumber} status is {$transactionStatus}");

        if (strtoupper($transactionStatus) === 'SUCCESS') {
            $order->status = Order::STATUS_CONFIRMED; // Mark as confirmed / Lunas
            if (!$order->paid_at) {
                $order->paid_at = now();
            }
        } else if (in_array(strtoupper($transactionStatus), ['FAILED', 'EXPIRED'])) {
            $order->status = Order::STATUS_CANCELLED;
        }

        $order->save();

        return response()->json(['message' => 'Notification handled']);
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
        $serverKey = config('services.midtrans.server_key');
        $signatureKey = $notification->signature_key;

        // Security: Verify Signature
        $calculatedSignature = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);
        
        // Security: Use hash_equals to prevent timing attacks
        if (!hash_equals($calculatedSignature, $signatureKey)) {
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
