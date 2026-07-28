<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\PageView;
use App\Models\Obituary;
use Symfony\Component\HttpFoundation\Response;

class TrackPageViews
{
    /**
     * Handle an incoming request and log pageview.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Skip non-GET requests, admin panel, assets, livewire, debugbar, or API calls
        if (!$request->isMethod('GET') || $request->is('admin*') || $request->is('storage*') || $request->is('images*') || $request->is('_debugbar*')) {
            return $response;
        }

        try {
            $url = $request->fullUrl();
            $path = $request->path();
            $routeName = $request->route() ? $request->route()->getName() : null;

            // Detect Device Type
            $userAgent = $request->userAgent() ?? '';
            $deviceType = 'desktop';
            if (preg_match('/(bot|crawl|spider|slurp|yahoo|bing|googlebot)/i', $userAgent)) {
                $deviceType = 'bot';
            } elseif (preg_match('/(android|iphone|ipad|mobile|ipod|blackberry|opera mini|iemobile)/i', $userAgent)) {
                $deviceType = (preg_match('/(ipad|tablet)/i', $userAgent)) ? 'tablet' : 'mobile';
            }

            // Parse Referer
            $referer = $request->header('referer');
            $refererHost = 'Direct / Bookmark';
            if (!empty($referer)) {
                $host = parse_url($referer, PHP_URL_HOST);
                if ($host && !str_contains($host, $request->getHost())) {
                    $refererHost = strtolower($host);
                    if (str_contains($refererHost, 'google')) $refererHost = 'Google Search';
                    elseif (str_contains($refererHost, 'facebook') || str_contains($refererHost, 'fb.me')) $refererHost = 'Facebook';
                    elseif (str_contains($refererHost, 'whatsapp')) $refererHost = 'WhatsApp';
                    elseif (str_contains($refererHost, 'twitter') || str_contains($refererHost, 't.co') || str_contains($refererHost, 'x.com')) $refererHost = 'Twitter / X';
                    elseif (str_contains($refererHost, 'bing')) $refererHost = 'Bing';
                    elseif (str_contains($refererHost, 'yahoo')) $refererHost = 'Yahoo';
                }
            }

            // Check if viewing specific Obituary Notice
            $obituaryId = null;
            if ($routeName === 'obituaries.show') {
                $slug = $request->route('slug');
                if ($slug) {
                    $ob = Obituary::where('slug', $slug)->first(['id']);
                    if ($ob) {
                        $obituaryId = $ob->id;
                    }
                }
            }

            PageView::create([
                'url' => substr($url, 0, 500),
                'route_name' => $routeName,
                'obituary_id' => $obituaryId,
                'ip_address' => $request->ip(),
                'user_agent' => substr($userAgent, 0, 500),
                'device_type' => $deviceType,
                'referer' => substr((string)$referer, 0, 500),
                'referer_host' => $refererHost,
                'created_at' => now(),
            ]);
        } catch (\Throwable $e) {
            // Silently handle any analytics logging exception so user page load is never interrupted
        }

        return $response;
    }
}
