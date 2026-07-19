<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Menu;

class GeminiService
{
    protected string $apiKey;
    protected string $model = 'gemini-3.5-flash';
    protected string $baseUrl = 'https://generativelanguage.googleapis.com/v1beta/models/';

    public function __construct()
    {
        $this->apiKey = env('GEMINI_API_KEY', '');
    }

    public function sendMessage(array $messages)
    {
        if (empty($this->apiKey)) {
            throw new \Exception("GEMINI_API_KEY is not set.");
        }

        // Fetch available menu items to build the system prompt
        $menus = Menu::where('is_available', true)->get();
        $menuList = [];
        foreach ($menus as $menu) {
            $menuList[] = sprintf(
                "- ID: %d, Name: %s, Price: Rp%s, Category: %s. Description: %s",
                $menu->id,
                $menu->name,
                number_format($menu->price, 0, ',', '.'),
                $menu->category,
                $menu->description ?? 'None'
            );
        }
        $menuString = implode("\n", $menuList);

        $systemPrompt = <<<PROMPT
                        You are Admin Kumaw (also known as MiMaw), a friendly, smart, and helpful ordering assistant for Kumaw X Atmosphr restaurant.
                        Your primary goal is to help customers order food by answering their questions about the menu and using the 'add_to_cart' tool when they want to order something.

                        Here is the current available menu:
                        {$menuString}

                        Rules:
                        1. Speak in friendly, polite, and casual Indonesian (e.g., use "Kak", "Boleh", "Siap").
                        2. Only recommend items that are on the menu.
                        3. If the user explicitly asks to order something, use the `add_to_cart` tool.
                        4. Always confirm back to the user what was added.
                        5. Keep your responses relatively short and direct.
                        6. Use emojis to make the conversation lively!
                        PROMPT;

        $tools = [
            [
                'functionDeclarations' => [
                    [
                        'name' => 'add_to_cart',
                        'description' => 'Add a specific menu item to the customer\'s cart.',
                        'parameters' => [
                            'type' => 'OBJECT',
                            'properties' => [
                                'menu_id' => [
                                    'type' => 'INTEGER',
                                    'description' => 'The ID of the menu item to add.'
                                ],
                                'quantity' => [
                                    'type' => 'INTEGER',
                                    'description' => 'The number of portions to add.'
                                ],
                                'notes' => [
                                    'type' => 'STRING',
                                    'description' => 'Any special requests or notes from the customer for this item (e.g., "pedes", "jangan pakai saus"). Leave empty if none.'
                                ]
                            ],
                            'required' => ['menu_id', 'quantity']
                        ]
                    ]
                ]
            ]
        ];

        try {
            $url = $this->baseUrl . $this->model . ':generateContent?key=' . $this->apiKey;

            $payload = [
                'systemInstruction' => [
                    'parts' => [
                        ['text' => $systemPrompt]
                    ]
                ],
                'contents' => $messages,
                'tools' => $tools
            ];

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->connectTimeout(30)->timeout(60)->post($url, $payload);

            if ($response->failed()) {
                Log::error('Gemini API Error', ['response' => $response->json()]);
                throw new \Exception("Gagal menghubungi AI Assistant.");
            }

            return $response->json();
        } catch (\Exception $e) {
            Log::error('Gemini API Exception', ['error' => $e->getMessage()]);
            throw $e;
        }
    }
}
