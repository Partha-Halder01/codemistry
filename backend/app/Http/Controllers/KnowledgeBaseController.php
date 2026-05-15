<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KnowledgeBase;
use App\Models\Service;

class KnowledgeBaseController extends Controller
{
    public function index()
    {
        $knowledgeBases = KnowledgeBase::orderBy('created_at', 'desc')->get();
        return response()->json($knowledgeBases);
    }

    public function store(Request $request)
    {
        $request->validate([
            'question' => 'required|string',
            'answer' => 'required|string',
            'content' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $entry = KnowledgeBase::create($request->all());

        return response()->json([
            'message' => 'Knowledge base entry created successfully',
            'entry' => $entry
        ], 201);
    }

    public function show($id)
    {
        $entry = KnowledgeBase::findOrFail($id);
        return response()->json($entry);
    }

    public function update(Request $request, $id)
    {
        $entry = KnowledgeBase::findOrFail($id);
        
        $request->validate([
            'question' => 'sometimes|required|string',
            'answer' => 'sometimes|required|string',
            'content' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $entry->update($request->all());

        return response()->json([
            'message' => 'Knowledge base entry updated successfully',
            'entry' => $entry
        ]);
    }

    public function destroy($id)
    {
        $entry = KnowledgeBase::findOrFail($id);
        $entry->delete();

        return response()->json([
            'message' => 'Knowledge base entry deleted successfully'
        ]);
    }

    public function getWebsiteContext()
    {
        $services = Service::with(['pricings'])->get();

        $websiteContext = "";
        foreach ($services as $service) {
            $websiteContext .= "=== Service: {$service->name} ===\n";
            if ($service->description) {
                $websiteContext .= "Description: {$service->description}\n";
            }

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

            if (is_array($service->faq) && !empty($service->faq)) {
                $websiteContext .= "FAQs:\n";
                foreach ($service->faq as $faq) {
                    if (!empty($faq['q']) && !empty($faq['a'])) {
                        $websiteContext .= "Q: {$faq['q']}\nA: {$faq['a']}\n";
                    }
                }
            }

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
            $websiteContext .= "\n";
        }

        return response()->json([
            'context' => $websiteContext
        ]);
    }
}
