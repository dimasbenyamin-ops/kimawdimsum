<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$order = \App\Models\Order::create([
    'order_number' => 'TEST-' . time(),
    'status' => \App\Models\Order::STATUS_PENDING_PAYMENT,
    'payment_method' => 'qris',
    'total_price' => 10000,
    'tax_amount' => 1000,
    'grand_total' => 11000,
    'type' => 'take_away'
]);

$item = \App\Models\OrderItem::create([
    'order_id' => $order->id,
    'menu_id' => 1,
    'quantity' => 1,
    'price' => 11000,
    'subtotal' => 11000
]);

$controller = new \App\Http\Controllers\PaymentController();
$response = $controller->getSnapToken($order);
echo $response->getContent();
