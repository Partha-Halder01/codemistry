<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PageView;
use Carbon\Carbon;

class AdminAnalyticsController extends Controller
{
    public function getDashboardData(Request $request)
    {
        $hasStart = $request->filled('start_date');
        $hasEnd   = $request->filled('end_date');

        if ($hasStart) {
            $startDate = Carbon::parse($request->query('start_date'))->startOfDay();
        } elseif ($hasEnd) {
            // explicit end with no start — go back 30 days from end
            $startDate = Carbon::parse($request->query('end_date'))->subDays(30)->startOfDay();
        } else {
            // "All Time" — use the earliest record we have, or fall back to 1 year ago
            $earliest  = PageView::min('created_at');
            $startDate = $earliest ? Carbon::parse($earliest)->startOfDay() : Carbon::now()->subYear()->startOfDay();
        }

        $endDate = $hasEnd
            ? Carbon::parse($request->query('end_date'))->endOfDay()
            : Carbon::now()->endOfDay();

        $query = PageView::whereBetween('created_at', [$startDate, $endDate]);

        $totalVisitors  = (clone $query)->distinct('session_id')->count('session_id');
        $totalPageViews = (clone $query)->count();

        // Average time per session = sum(max time_spent per session+path) / sessions
        // Time spent is already accumulated per (session, path), so summing is fine
        $totalTimeSpent = (clone $query)->sum('time_spent');
        $averageTime    = $totalVisitors > 0 ? floor($totalTimeSpent / $totalVisitors) : 0;

        // Bounce rate = % of sessions that viewed only 1 page
        $sessionPageCounts = (clone $query)
            ->select('session_id', \DB::raw('COUNT(DISTINCT path) as pages'))
            ->groupBy('session_id')
            ->get();
        $bouncedSessions = $sessionPageCounts->where('pages', 1)->count();
        $bounceRate = $totalVisitors > 0 ? round(($bouncedSessions / $totalVisitors) * 100, 1) : 0;

        // Live users — sessions pinged in the last 5 minutes
        $liveUsers = PageView::where('last_ping_at', '>=', Carbon::now()->subMinutes(5))
            ->distinct('session_id')
            ->count('session_id');

        $topPages = (clone $query)
            ->select('path', \DB::raw('count(*) as views'), \DB::raw('FLOOR(AVG(time_spent)) as avg_time'))
            ->groupBy('path')
            ->orderByDesc('views')
            ->limit(10)
            ->get();

        $topServices = (clone $query)
            ->whereNotNull('service_id')
            ->with('service:id,name')
            ->select('service_id', \DB::raw('count(*) as views'))
            ->groupBy('service_id')
            ->orderByDesc('views')
            ->limit(10)
            ->get()
            ->map(function ($item) {
                return [
                    'name'  => $item->service ? $item->service->name : 'Unknown Service',
                    'views' => $item->views,
                ];
            });

        $topCountries = (clone $query)
            ->whereNotNull('country')
            ->select('country', \DB::raw('count(DISTINCT session_id) as visitors'))
            ->groupBy('country')
            ->orderByDesc('visitors')
            ->limit(5)
            ->get();

        $topCities = (clone $query)
            ->whereNotNull('city')
            ->select('city', 'country', \DB::raw('count(DISTINCT session_id) as visitors'))
            ->groupBy('city', 'country')
            ->orderByDesc('visitors')
            ->limit(5)
            ->get();

        // Device + browser breakdown (visitors per type)
        $deviceBreakdown = (clone $query)
            ->whereNotNull('device_type')
            ->select('device_type', \DB::raw('count(DISTINCT session_id) as visitors'))
            ->groupBy('device_type')
            ->orderByDesc('visitors')
            ->get();

        $browserBreakdown = (clone $query)
            ->whereNotNull('browser')
            ->select('browser', \DB::raw('count(DISTINCT session_id) as visitors'))
            ->groupBy('browser')
            ->orderByDesc('visitors')
            ->limit(6)
            ->get();

        $topReferrers = (clone $query)
            ->whereNotNull('referrer')
            ->where('referrer', '!=', '')
            ->select('referrer', \DB::raw('count(DISTINCT session_id) as visitors'))
            ->groupBy('referrer')
            ->orderByDesc('visitors')
            ->limit(6)
            ->get();

        // Daily traffic trend (visitors + views per day across the range)
        $dailyRaw = (clone $query)
            ->select(
                \DB::raw('DATE(created_at) as date'),
                \DB::raw('COUNT(*) as views'),
                \DB::raw('COUNT(DISTINCT session_id) as visitors')
            )
            ->groupBy(\DB::raw('DATE(created_at)'))
            ->orderBy('date')
            ->get()
            ->keyBy(fn ($r) => (string) $r->date);

        // Pad missing days with zeros so the chart is continuous
        $trend  = [];
        $cursor = $startDate->copy()->startOfDay();
        $stop   = $endDate->copy()->startOfDay();
        // cap at ~120 buckets to keep response small for "All Time" on big ranges
        $totalDays = $cursor->diffInDays($stop) + 1;
        $stride    = max(1, (int) ceil($totalDays / 120));
        while ($cursor->lte($stop)) {
            $key = $cursor->toDateString();
            $row = $dailyRaw->get($key);
            $trend[] = [
                'date'     => $key,
                'views'    => $row ? (int) $row->views : 0,
                'visitors' => $row ? (int) $row->visitors : 0,
            ];
            $cursor->addDays($stride);
        }

        return response()->json([
            'summary' => [
                'total_visitors' => $totalVisitors,
                'total_views'    => $totalPageViews,
                'average_time'   => $this->formatTime($averageTime),
                'live_users'     => $liveUsers,
                'bounce_rate'    => $bounceRate,
                'pages_per_session' => $totalVisitors > 0
                    ? round($totalPageViews / $totalVisitors, 2)
                    : 0,
            ],
            'range' => [
                'start' => $startDate->toDateString(),
                'end'   => $endDate->toDateString(),
            ],
            'trend'             => $trend,
            'top_pages'         => $topPages,
            'top_services'      => $topServices,
            'top_countries'     => $topCountries,
            'top_cities'        => $topCities,
            'device_breakdown'  => $deviceBreakdown,
            'browser_breakdown' => $browserBreakdown,
            'top_referrers'     => $topReferrers,
        ]);
    }

    private function formatTime($seconds)
    {
        $minutes = floor($seconds / 60);
        $remainingSeconds = $seconds % 60;
        return sprintf('%dm %02ds', $minutes, $remainingSeconds);
    }
}
