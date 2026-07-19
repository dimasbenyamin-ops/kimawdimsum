<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Log;

/**
 * WhatsAppNotificationService
 *
 * A wrapper service for integrating a WhatsApp API Gateway (e.g., Twilio, WATI, Qiscus).
 * Currently, it simulates sending a message by logging the payload.
 */
class WhatsAppNotificationService
{
    /**
     * Send an "Order Ready" notification to the customer.
     *
     * @param Order $order
     * @return bool True if successfully sent, false otherwise.
     */
    public function sendOrderReadyNotification(Order $order): bool
    {
        if (empty($order->phone_number)) {
            Log::warning("WhatsApp Notification skipped: Order #{$order->order_number} has no phone number.");
            return false;
        }

        // Format the message
        $message = "Halo {$order->customer_name}!\n\n"
                 . "Pesanan kamu dengan nomor *{$order->order_number}* sudah SIAP DIAMBIL 🥟✨\n\n"
                 . "Total Tagihan: {$order->formattedTotal}\n"
                 . "Terima kasih telah memesan di Kumaw X Atmosphr!";

        // Simulate API Payload
        $payload = [
            'to'      => $this->formatPhoneNumber($order->phone_number),
            'message' => $message,
        ];

        // TODO: Replace this logging with actual API call (e.g. Http::post('https://api.whatsapp-gateway.com/send', $payload))
        Log::info("=== WHATSAPP NOTIFICATION SENT ===");
        Log::info(json_encode($payload, JSON_PRETTY_PRINT));
        Log::info("==================================");

        return true;
    }

    /**
     * Format phone number to international format (e.g. 0812... to 62812...).
     */
    private function formatPhoneNumber(string $phone): string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }
        return $phone;
    }
}
