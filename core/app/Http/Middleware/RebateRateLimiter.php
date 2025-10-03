<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RebateRateLimiter
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $maxAttempts = '60', string $decayMinutes = '1'): Response
    {
        $key = $this->resolveRequestSignature($request);

        // Different limits for different actions
        if ($request->routeIs('user.product.upload.store')) {
            // Stricter limits for uploads
            $maxAttempts = '10';
            $decayMinutes = '60'; // 10 uploads per hour
        } elseif ($request->routeIs('api.rebate.*')) {
            // API rate limiting
            $maxAttempts = '100';
            $decayMinutes = '60'; // 100 API calls per hour
        }

        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            $seconds = RateLimiter::availableIn($key);
            
            // Log rate limit hit
            Log::warning('Rate limit exceeded', [
                'ip' => $request->ip(),
                'user_id' => auth()->id(),
                'route' => $request->route()?->getName(),
                'attempts' => RateLimiter::attempts($key),
                'available_in' => $seconds
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Too many requests. Please try again in ' . $seconds . ' seconds.',
                'retry_after' => $seconds
            ], 429);
        }

        RateLimiter::hit($key, $decayMinutes * 60);

        $response = $next($request);

        $response->headers->set('X-RateLimit-Limit', $maxAttempts);
        $response->headers->set('X-RateLimit-Remaining', RateLimiter::remaining($key, $maxAttempts));
        $response->headers->set('X-RateLimit-Reset', now()->addMinutes($decayMinutes)->timestamp);
        
        return $response;
    }

    /**
     * Resolve the request signature for rate limiting
     */
    protected function resolveRequestSignature(Request $request): string
    {
        $userId = Auth::id();
        $ip = $request->ip();
        $route = $request->route()?->getName() ?? 'unknown';

        // Use user ID if authenticated, otherwise IP
        $identifier = $userId ? "user:{$userId}" : "ip:{$ip}";
        
        return "rebate_rate_limit:{$identifier}:{$route}";
    }
}