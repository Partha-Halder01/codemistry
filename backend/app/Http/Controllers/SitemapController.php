<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\Service;
use Illuminate\Support\Carbon;

class SitemapController extends Controller
{
    public function index()
    {
        $base = rtrim(config('app.frontend_url', env('FRONTEND_URL', 'https://codemistry.in')), '/');

        $urls = [
            ['loc' => $base . '/',          'changefreq' => 'weekly',  'priority' => '1.0',  'lastmod' => now()],
            ['loc' => $base . '/services',  'changefreq' => 'weekly',  'priority' => '0.9',  'lastmod' => now()],
            ['loc' => $base . '/about',     'changefreq' => 'monthly', 'priority' => '0.6',  'lastmod' => now()],
            ['loc' => $base . '/contact',   'changefreq' => 'monthly', 'priority' => '0.6',  'lastmod' => now()],
            ['loc' => $base . '/blog',      'changefreq' => 'daily',   'priority' => '0.9',  'lastmod' => now()],
        ];

        foreach (Service::all(['slug', 'updated_at']) as $service) {
            if (!$service->slug) continue;
            $urls[] = [
                'loc'        => $base . '/services/' . $service->slug,
                'changefreq' => 'monthly',
                'priority'   => '0.8',
                'lastmod'    => $service->updated_at ?? now(),
            ];
        }

        foreach (BlogPost::published()->get(['slug', 'updated_at', 'published_at']) as $post) {
            $urls[] = [
                'loc'        => $base . '/blog/' . $post->slug,
                'changefreq' => 'monthly',
                'priority'   => '0.7',
                'lastmod'    => $post->updated_at ?? $post->published_at ?? now(),
            ];
        }

        $cityServiceCombos = [
            ['service' => 'web-development', 'cities' => ['delhi-ncr', 'mumbai', 'bangalore', 'hyderabad', 'chennai', 'kolkata', 'pune']],
            ['service' => 'ai-integration',  'cities' => ['delhi-ncr', 'mumbai', 'bangalore', 'hyderabad', 'chennai', 'kolkata', 'pune']],
        ];
        foreach ($cityServiceCombos as $combo) {
            foreach ($combo['cities'] as $city) {
                $urls[] = [
                    'loc'        => $base . '/services/' . $combo['service'] . '-' . $city,
                    'changefreq' => 'monthly',
                    'priority'   => '0.7',
                    'lastmod'    => now(),
                ];
            }
        }

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        foreach ($urls as $u) {
            $lastmod = $u['lastmod'] instanceof Carbon ? $u['lastmod']->toAtomString() : Carbon::parse($u['lastmod'])->toAtomString();
            $xml .= "  <url>\n";
            $xml .= "    <loc>" . htmlspecialchars($u['loc'], ENT_XML1) . "</loc>\n";
            $xml .= "    <lastmod>{$lastmod}</lastmod>\n";
            $xml .= "    <changefreq>{$u['changefreq']}</changefreq>\n";
            $xml .= "    <priority>{$u['priority']}</priority>\n";
            $xml .= "  </url>\n";
        }
        $xml .= '</urlset>';

        return response($xml, 200, ['Content-Type' => 'application/xml']);
    }
}
