<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AiChatHistory;
use Carbon\Carbon;

class AdminChatHistoryController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->query('start_date') ? Carbon::parse($request->query('start_date'))->startOfDay() : Carbon::now()->subDays(30)->startOfDay();
        $endDate = $request->query('end_date') ? Carbon::parse($request->query('end_date'))->endOfDay() : Carbon::now()->endOfDay();

        $query = AiChatHistory::whereBetween('created_at', [$startDate, $endDate]);

        // Key Metrics
        $totalChats = (clone $query)->count();
        
        $todayStart = Carbon::today();
        $todayEnd = Carbon::today()->endOfDay();
        $todayChats = AiChatHistory::whereBetween('created_at', [$todayStart, $todayEnd])->count();
        
        $totalSessions = (clone $query)->distinct('session_id')->count('session_id');

        // Fetch logs (latest first)
        $logs = (clone $query)->orderByDesc('created_at')->get()->map(function ($chat) {
            return [
                'id' => $chat->id,
                'session_id' => $chat->session_id ?? 'Unknown',
                'user_message' => $chat->user_message,
                'ai_response' => $chat->ai_response,
                'created_at' => $chat->created_at->toIso8601String(),
                'formatted_date' => $chat->created_at->format('M d, Y h:i A')
            ];
        });

        return response()->json([
            'summary' => [
                'total_chats' => $totalChats,
                'today_chats' => $todayChats,
                'total_sessions' => $totalSessions
            ],
            'logs' => $logs
        ]);
    }
}
