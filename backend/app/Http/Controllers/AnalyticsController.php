<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PageView;
use Illuminate\Support\Facades\Http;

class AnalyticsController extends Controller
{
    public function track(Request $request)
    {
        $request->validate([
            'session_id' => 'required|string',
            'path' => 'required|string',
            'time_spent' => 'required|integer'
        ]);

        $ip = $request->ip();

        $pageView = PageView::where('session_id', $request->session_id)
            ->where('path', $request->path)
            ->where('created_at', '>=', now()->subHour())
            ->first();

        if ($pageView) {
            $pageView->time_spent = max($pageView->time_spent, $request->time_spent);
            $pageView->last_ping_at = now();
            $pageView->save();
        } else {
            $geoData = $this->getGeoLocation($ip);

            $serviceId = null;
            if (preg_match('/^\/services\/([^\/]+)$/', $request->path, $matches)) {
                $service = \App\Models\Service::where('slug', $matches[1])->first();
                if ($service) {
                    $serviceId = $service->id;
                }
            }

            $userAgent = $request->userAgent() ?? '';
            $device    = $this->detectDevice($userAgent);
            $browser   = $this->detectBrowser($userAgent);
            $referrer  = $request->input('referrer') ?: $request->headers->get('referer');
            if ($referrer) {
                $host = parse_url($referrer, PHP_URL_HOST);
                $referrer = $host ?: substr($referrer, 0, 250);
            }

            $pageView = PageView::create([
                'session_id'   => $request->session_id,
                'ip_address'   => $ip,
                'user_agent'   => substr($userAgent, 0, 500),
                'device_type'  => $device,
                'browser'      => $browser,
                'referrer'     => $referrer,
                'country'      => $geoData['country'] ?? null,
                'city'         => $geoData['city'] ?? null,
                'path'         => $request->path,
                'service_id'   => $serviceId,
                'time_spent'   => $request->time_spent,
                'last_ping_at' => now(),
            ]);
        }

        return response()->json(['success' => true]);
    }

    private function detectDevice(string $ua): string
    {
        if ($ua === '') return 'Unknown';
        $ua = strtolower($ua);
        if (preg_match('/(tablet|ipad|playbook|silk)|(android(?!.*mobi))/i', $ua)) return 'Tablet';
        if (preg_match('/mobile|iphone|ipod|android|blackberry|opera mini|iemobile|wpdesktop/i', $ua)) return 'Mobile';
        return 'Desktop';
    }

    private function detectBrowser(string $ua): string
    {
        if ($ua === '') return 'Unknown';
        if (stripos($ua, 'Edg/') !== false) return 'Edge';
        if (stripos($ua, 'OPR/') !== false || stripos($ua, 'Opera') !== false) return 'Opera';
        if (stripos($ua, 'Chrome') !== false && stripos($ua, 'Safari') !== false) return 'Chrome';
        if (stripos($ua, 'Firefox') !== false) return 'Firefox';
        if (stripos($ua, 'Safari') !== false) return 'Safari';
        if (stripos($ua, 'MSIE') !== false || stripos($ua, 'Trident') !== false) return 'IE';
        return 'Other';
    }

    private function getGeoLocation($ip)
    {
        if ($ip === '127.0.0.1' || $ip === '::1') return ['country' => 'Localhost', 'city' => 'Local'];

        try {
            $response = Http::timeout(2)->get("http://ip-api.com/json/{$ip}?fields=status,country,city");
            if ($response->successful() && $response->json('status') === 'success') {
                return [
                    'country' => $response->json('country'),
                    'city' => $response->json('city')
                ];
            }
        } catch (\Exception $e) {
            // Silently fail geo-lookup
        }
        return ['country' => 'Unknown', 'city' => 'Unknown'];
    }
}
