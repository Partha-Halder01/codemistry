<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\ServicePricing;

class AdminServiceController extends Controller
{
    public function index()
    {
        $services = Service::with('pricings')->get();
        return response()->json($services);
    }

    public function show($id)
    {
        $service = Service::with('pricings')->findOrFail($id);
        return response()->json($service);
    }

    public function store(Request $request)
    {
        // Decode JSON arrays sent via multipart/form-data and cast prices to integers
        $pricings = $request->input('pricings');
        if (is_string($pricings)) {
            $pricings = json_decode($pricings, true);
        }
        if (is_array($pricings)) {
            foreach ($pricings as &$p) {
                if (isset($p['price']) && $p['price'] !== '') $p['price'] = (int) round((float) $p['price']);
                if (isset($p['end_price']) && $p['end_price'] !== '') $p['end_price'] = (int) round((float) $p['end_price']);
            }
            $request->merge(['pricings' => $pricings]);
        }

        foreach (['faq', 'process_steps'] as $field) {
            if ($request->has($field) && is_string($request->$field)) {
                $request->merge([$field => json_decode($request->$field, true)]);
            }
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:services,slug',
            'description' => 'required|string',
            'full_price' => 'nullable|integer',
            'deposit_price' => 'nullable|integer',
            'features' => 'nullable|string',
            'cover_image' => 'nullable|image|max:5120',
            'cta_image' => 'nullable|image|max:5120',
            'faq' => 'nullable|array',
            'process_steps' => 'nullable|array',
            'is_featured' => 'boolean',
            'meta_title' => 'nullable|string|max:80',
            'meta_description' => 'nullable|string|max:200',
            'meta_keywords' => 'nullable|string|max:500',
            'pricings' => 'nullable|array',
            'pricings.*.plan_name' => 'required|string',
            'pricings.*.price' => 'required|integer',
            'pricings.*.end_price' => 'nullable|integer',
            'pricings.*.features' => 'nullable|array',
            'pricings.*.is_popular' => 'nullable|boolean',
        ]);

        $serviceData = $request->only(['name', 'slug', 'description', 'full_price', 'deposit_price', 'features', 'faq', 'process_steps', 'is_featured', 'meta_title', 'meta_description', 'meta_keywords']);

        if ($request->hasFile('cover_image')) {
            $path = $request->file('cover_image')->store('service_covers', 'public');
            $serviceData['cover_image_path'] = $path;
        }

        if ($request->hasFile('cta_image')) {
            $path = $request->file('cta_image')->store('service_cta_images', 'public');
            $serviceData['cta_image_path'] = $path;
        }

        $service = Service::create($serviceData);

        if ($request->has('pricings')) {
            foreach ($request->pricings as $pricing) {
                $service->pricings()->create($pricing);
            }
        }

        return response()->json([
            'message' => 'Service created successfully',
            'service' => $service->load('pricings')
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $service = Service::findOrFail($id);
        
        // Decode JSON arrays sent via multipart/form-data and cast prices to integers
        $pricings = $request->input('pricings');
        if (is_string($pricings)) {
            $pricings = json_decode($pricings, true);
        }
        if (is_array($pricings)) {
            foreach ($pricings as &$p) {
                if (isset($p['price']) && $p['price'] !== '') $p['price'] = (int) round((float) $p['price']);
                if (isset($p['end_price']) && $p['end_price'] !== '') $p['end_price'] = (int) round((float) $p['end_price']);
            }
            $request->merge(['pricings' => $pricings]);
        }

        foreach (['faq', 'process_steps'] as $field) {
            if ($request->has($field) && is_string($request->$field)) {
                $request->merge([$field => json_decode($request->$field, true)]);
            }
        }

        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:services,slug,' . $id,
            'description' => 'sometimes|required|string',
            'full_price' => 'nullable|integer',
            'deposit_price' => 'nullable|integer',
            'features' => 'nullable|string',
            'cover_image' => 'nullable|image|max:5120',
            'cta_image' => 'nullable|image|max:5120',
            'faq' => 'nullable|array',
            'process_steps' => 'nullable|array',
            'is_featured' => 'boolean',
            'meta_title' => 'nullable|string|max:80',
            'meta_description' => 'nullable|string|max:200',
            'meta_keywords' => 'nullable|string|max:500',
            'pricings' => 'nullable|array',
            'pricings.*.id' => 'nullable|integer',
            'pricings.*.plan_name' => 'required|string',
            'pricings.*.price' => 'required|integer',
            'pricings.*.end_price' => 'nullable|integer',
            'pricings.*.features' => 'nullable|array',
            'pricings.*.is_popular' => 'nullable|boolean',
        ]);

        $serviceData = $request->only(['name', 'slug', 'description', 'full_price', 'deposit_price', 'features', 'faq', 'process_steps', 'is_featured', 'meta_title', 'meta_description', 'meta_keywords']);

        if ($request->hasFile('cover_image')) {
            // Delete old image if exists
            if ($service->cover_image_path && \Storage::disk('public')->exists($service->cover_image_path)) {
                \Storage::disk('public')->delete($service->cover_image_path);
            }
            $path = $request->file('cover_image')->store('service_covers', 'public');
            $serviceData['cover_image_path'] = $path;
        }

        if ($request->hasFile('cta_image')) {
            // Delete old image if exists
            if ($service->cta_image_path && \Storage::disk('public')->exists($service->cta_image_path)) {
                \Storage::disk('public')->delete($service->cta_image_path);
            }
            $path = $request->file('cta_image')->store('service_cta_images', 'public');
            $serviceData['cta_image_path'] = $path;
        }

        $service->update($serviceData);

        if ($request->has('pricings')) {
            // Keep track of provided pricing IDs to delete removed ones
            $providedPricingIds = collect($request->pricings)->pluck('id')->filter()->toArray();
            
            // Delete pricings not in the provided list
            $service->pricings()->whereNotIn('id', $providedPricingIds)->delete();

            foreach ($request->pricings as $pricingData) {
                if (isset($pricingData['id'])) {
                    // Update existing pricing
                    $pricing = ServicePricing::findOrFail($pricingData['id']);
                    $pricing->update($pricingData);
                } else {
                    // Create new pricing
                    $service->pricings()->create($pricingData);
                }
            }
        }

        return response()->json([
            'message' => 'Service updated successfully',
            'service' => $service->load('pricings')
        ]);
    }

    public function destroy($id)
    {
        $service = Service::findOrFail($id);
        $service->delete(); // This will cascade delete pricings because of our migration setup

        return response()->json([
            'message' => 'Service deleted successfully'
        ]);
    }
}
