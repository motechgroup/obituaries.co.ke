<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\BlockedIp;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckBlockedIp
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $ip = $request->ip();

        if (BlockedIp::isBlocked($ip)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access Denied. Your IP address has been flagged and blocked due to suspicious security violations.',
                ], 403);
            }

            abort(403, 'Access Denied: Your IP address has been blocked for security and fraud prevention reasons. If you believe this is an error, contact support.');
        }

        return $next($request);
    }
}
