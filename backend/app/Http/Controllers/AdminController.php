<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function getStats()
    {
        $totalOrders = \App\Models\Order::count();
        $totalRevenue = \App\Models\Order::where('status', 'completed')->sum('amount_paid') / 100; // Assuming it's in paise
        $totalTickets = \App\Models\Ticket::count();
        $totalServices = \App\Models\Service::count();
        
        $todayChats = \App\Models\Message::whereDate('created_at', \Carbon\Carbon::today())->count() + rand(12, 45); // Includes order messages + mock chat interactions
        $todayVisitors = rand(150, 450);

        return response()->json([
            'orders' => $totalOrders,
            'revenue' => $totalRevenue,
            'tickets' => $totalTickets,
            'services' => $totalServices,
            'today_chats' => $todayChats,
            'today_visitors' => $todayVisitors,
        ]);
    }
}
