<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\GeminiService;
use App\Models\Menu;

class ChatbotController extends Controller
{
    protected GeminiService $geminiService;

    public function __construct(GeminiService $geminiService)
    {
        $this->geminiService = $geminiService;
    }

    public function chat(Request $request)
    {
        $validated = $request->validate([
            'message' => 'required|string|max:1000'
        ]);

        $userMessage = $validated['message'];
        $history = session('chat_history', []);

        // Append user message to history in Gemini format
        $history[] = [
            'role' => 'user',
            'parts' => [
                ['text' => $userMessage]
            ]
        ];

        try {
            // First call to Gemini
            $response = $this->geminiService->sendMessage($history);
            
            // Extract the actual assistant response message
            // Response structure from Gemini:
            // { "candidates": [ { "content": { "role": "model", "parts": [ ... ] } } ] }
            
            if (!isset($response['candidates'][0]['content'])) {
                throw new \Exception("Invalid response format from Gemini.");
            }

            $modelContent = $response['candidates'][0]['content'];
            $history[] = $modelContent; // append model's raw response to history

            // Check if there's a tool use (functionCall)
            $hasToolUse = false;
            $toolResponses = [];

            foreach ($modelContent['parts'] as $part) {
                if (isset($part['functionCall'])) {
                    $hasToolUse = true;
                    $funcCall = $part['functionCall'];
                    
                    if ($funcCall['name'] === 'add_to_cart') {
                        $args = $funcCall['args'];
                        $menuId = $args['menu_id'] ?? null;
                        $quantity = $args['quantity'] ?? 1;
                        $notes = $args['notes'] ?? null;

                        $resultText = $this->addToCartLogic($menuId, $quantity, $notes);

                        $toolResponses[] = [
                            'functionResponse' => [
                                'name' => 'add_to_cart',
                                'response' => [
                                    'result' => $resultText
                                ]
                            ]
                        ];
                    }
                }
            }

            // If a tool was used, we need to send the functionResponse back to Gemini so it can formulate the final answer
            if ($hasToolUse) {
                $history[] = [
                    'role' => 'user',
                    'parts' => $toolResponses
                ];

                $finalResponse = $this->geminiService->sendMessage($history);
                $finalModelContent = $finalResponse['candidates'][0]['content'];
                $history[] = $finalModelContent;

                session(['chat_history' => $history]);

                // Extract text from the final response
                $replyText = $this->extractText($finalModelContent['parts']);
                return response()->json([
                    'reply' => $replyText,
                    'cart_updated' => true
                ]);
            }

            // If no tool was used, just save history and return
            session(['chat_history' => $history]);
            $replyText = $this->extractText($modelContent['parts']);

            return response()->json([
                'reply' => $replyText,
                'cart_updated' => false
            ]);

        } catch (\Exception $e) {
            // Remove the last user message from history on error to allow retry
            array_pop($history);
            session(['chat_history' => $history]);

            return response()->json([
                'reply' => 'Maaf, sistem chatbot sedang mengalami gangguan. Silakan coba lagi nanti ya! 🙏',
                'error' => $e->getMessage(),
                'cart_updated' => false
            ], 500);
        }
    }

    public function clearHistory()
    {
        session()->forget('chat_history');
        return response()->json(['status' => 'cleared']);
    }

    private function addToCartLogic($menuId, $quantity, $notes)
    {
        $menu = Menu::available()->find($menuId);
        if (!$menu) {
            return "Failed: Menu ID $menuId not found or not available.";
        }

        if ($quantity < 1) $quantity = 1;

        $cart = session('cart', []);

        // Find if exists
        $found = false;
        foreach ($cart as &$item) {
            if ($item['menu_id'] == $menu->id && ($item['notes'] ?? null) == $notes) {
                $item['quantity'] += $quantity;
                $found = true;
                break;
            }
        }

        if (!$found) {
            $cart[] = [
                'menu_id' => $menu->id,
                'menu_name' => $menu->name,
                'menu_category' => $menu->category,
                'unit_price' => $menu->price,
                'quantity' => $quantity,
                'notes' => $notes,
            ];
        }

        session(['cart' => $cart]);

        return "Success: Added $quantity portion(s) of {$menu->name} to the cart.";
    }

    private function extractText(array $parts)
    {
        $text = "";
        foreach ($parts as $part) {
            if (isset($part['text'])) {
                $text .= $part['text'];
            }
        }
        return $text;
    }
}
