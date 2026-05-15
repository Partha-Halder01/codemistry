<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;

class ServiceController extends Controller
{
    public function index(Request $request)
    {
        $query = Service::with('pricings');
        
        if ($request->has('featured') && in_array($request->featured, ['true', '1', 1, true], true)) {
            $query->where('is_featured', 1);
        }
        
        $services = $query->get();
        return response()->json($services);
    }

    public function show($identifier)
    {
        $query = Service::with('pricings');

        if (is_numeric($identifier)) {
            $service = $query->findOrFail($identifier);
        } else {
            $service = $query->where('slug', $identifier)->firstOrFail();
        }

        return response()->json($service);
    }
    
    public function toggleFeatured(Request $request, $id)
    {
        $service = Service::findOrFail($id);
        $service->is_featured = !$service->is_featured;
        $service->save();
        
        return response()->json([
            'message' => 'Service featured status updated successfully',
            'service' => $service
        ]);
    }
}
