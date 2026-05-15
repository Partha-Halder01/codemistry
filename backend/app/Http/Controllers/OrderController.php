<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // If the user is an admin, return all orders
        if ($user instanceof \App\Models\AdminUser) {
            $orders = Order::with(['service', 'user'])->get();
            return response()->json($orders);
        }

        // Return orders specific to the regular user
        $orders = $user->orders()->with('service')->get();
        return response()->json($orders);
    }

    public function adminIndex()
    {
        $orders = Order::with(['service', 'user'])->latest()->get();
        return response()->json($orders);
    }

    public function show(Request $request, $id)
    {
        $user = $request->user();

        if ($user instanceof \App\Models\AdminUser) {
            $order = Order::with('service', 'messages')->findOrFail($id);
            return response()->json($order);
        }

        $order = $user->orders()->with('service', 'messages')->findOrFail($id);
        return response()->json($order);
    }

    public function store(Request $request)
    {
        $request->validate([
            'service_id' => 'required|exists:services,id',
            'payment_type' => 'required|in:full,deposit',
            'total_service_price' => 'required|integer',
            'amount_paid' => 'required|integer',
            'razorpay_payment_id' => 'required|string',
        ]);

        $user = $request->user();

        $order = $user->orders()->create([
            'order_uid' => 'ORD-' . strtoupper(uniqid()),
            'service_id' => $request->service_id,
            'payment_type' => $request->payment_type,
            'total_service_price' => $request->total_service_price,
            'amount_paid' => $request->amount_paid,
            'razorpay_payment_id' => $request->razorpay_payment_id,
            'coupon_code' => $request->coupon_code,
            'status' => 'Paid',
        ]);

        return response()->json($order, 201);
    }
}
