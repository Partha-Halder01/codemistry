<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KnowledgeBase;
use App\Models\Service;
use Illuminate\Support\Facades\Http;

class ChatbotController extends Controller
{
    public function chat(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
            'history' => 'nullable|array'
        ]);

        $userMessage = $request->message;

        // 1. Fetch active knowledge base
        $knowledge = KnowledgeBase::where('is_active', true)->get();

        // 2. Fetch website content (services, FAQs, features, pricing ranges)
        $services = Service::with(['pricings'])->get();

        $websiteContext = "Website content summary for Codemistry (services shown on the public site):\n";
        foreach ($services as $service) {
            $websiteContext .= "\n=== Service: {$service->name} ===\n";
            if ($service->description) {
                $websiteContext .= "Description: {$service->description}\n";
            }

            // Features can be stored as text or array
            $features = [];
            if (is_array($service->features)) {
                $features = $service->features;
            } elseif (is_string($service->features)) {
                $features = array_filter(preg_split("/\r\n|\n|\r/", $service->features));
            }
            if (!empty($features)) {
                $websiteContext .= "Key features:\n";
                foreach ($features as $feat) {
                    $websiteContext .= "- {$feat}\n";
                }
            }

            // FAQs (if present)
            if (is_array($service->faq) && !empty($service->faq)) {
                $websiteContext .= "FAQs:\n";
                foreach ($service->faq as $faq) {
                    if (!empty($faq['q']) && !empty($faq['a'])) {
                        $websiteContext .= "Q: {$faq['q']}\nA: {$faq['a']}\n";
                    }
                }
            }

            // Pricing overview (range only, not every plan detail)
            if ($service->pricings && $service->pricings->count() > 0) {
                $prices = $service->pricings->pluck('price')->filter(fn ($p) => $p !== null)->all();
                if (!empty($prices)) {
                    $min = min($prices);
                    $max = max($prices);
                    if ($min === $max) {
                        $websiteContext .= "Typical price: INR {$min}\n";
                    } else {
                        $websiteContext .= "Typical price range: INR {$min} - INR {$max}\n";
                    }
                }
            }
        }

        // 3. Build system instructions combining knowledge base and website content
        $systemInstructions = "You are a helpful AI assistant for Codemistry, a software development company.\n"
            . "Use the knowledge base entries and website content below as your primary source of truth when answering questions about Codemistry, its services, pricing, policies, and workflows.\n"
            . "- Always prefer specific facts from the knowledge base or website content.\n"
            . "- If a user asks a general technology question (not specific to Codemistry), you may answer using your own knowledge.\n"
            . "- If the question is specifically about Codemistry and the answer is not present in the knowledge base or website content, do not invent details; instead, politely say you are not sure and suggest contacting Codemistry support.\n\n"
            . "- Always reply in the same language (and script) as the user's latest message. If the user mixes languages, reply in the dominant language they used.\n"
            . "- Format answers in clean Markdown with short sections, bold highlights, and bullet points for clarity.\n"
            . "- Use simple visual icons/emoji in headings or bullets when helpful (for example: \xE2\x9C\x85, \xF0\x9F\x92\xB0, \xF0\x9F\x93\x8C), but keep it professional and concise.\n\n"
            . "=== Knowledge Base ===\n";

        foreach ($knowledge as $kb) {
            $systemInstructions .= "Q: {$kb->question}\nA: {$kb->answer}\n";
            if (!empty($kb->content)) {
                $systemInstructions .= "Additional content:\n{$kb->content}\n";
            }
            $systemInstructions .= "\n";
        }

        $systemInstructions .= "\n=== Website Content ===\n" . $websiteContext . "\n";

        // Using Google Gemini API (gemini-2.5-flash-lite — lowest token cost in the family)
        $apiKey = env('GEMINI_API_KEY', 'AIzaSyA1NrGO5wW4Gork6xa2OixlJSXRG7LAc-Y');
        $model  = env('GEMINI_MODEL', 'gemini-2.5-flash-lite');

        try {
            $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";

            $contents = [];
            if ($request->has('history') && is_array($request->history)) {
                foreach ($request->history as $msg) {
                    $contents[] = [
                        'role'  => $msg['role'] === 'user' ? 'user' : 'model',
                        'parts' => [['text' => $msg['content']]],
                    ];
                }
            }
            $contents[] = [
                'role'  => 'user',
                'parts' => [['text' => $userMessage]],
            ];

            $payload = [
                'system_instruction' => [
                    'parts' => [['text' => $systemInstructions]],
                ],
                'contents' => $contents,
                'generationConfig' => [
                    'temperature'     => 0.7,
                    'maxOutputTokens' => 800,
                ],
            ];

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])
            ->timeout(60)
            ->post($url, $payload);

            if ($response->successful()) {
                $data = $response->json();
                $reply = $data['candidates'][0]['content']['parts'][0]['text']
                    ?? 'Sorry, I could not generate a response.';
                
                // Save AI chat history
                try {
                    \App\Models\AiChatHistory::create([
                        'session_id' => $request->input('session_id'),
                        'user_message' => $userMessage,
                        'ai_response' => $reply
                    ]);
                } catch (\Exception $dbError) {
                    \Log::error('Failed to log AI chat: ' . $dbError->getMessage());
                }

                return response()->json([
                    'reply' => $reply
                ]);
            } else {
                \Log::error('Gemini API Error: ' . $response->body());
                return response()->json([
                    'reply' => 'I am sorry, but I encountered an error communicating with my AI brain.'
                ], 500);
            }

        } catch (\Exception $e) {
            \Log::error('Chatbot Exception: ' . $e->getMessage());
            return response()->json([
                'reply' => 'An unexpected error occurred. Please try again later.'
            ], 500);
        }
    }
}
