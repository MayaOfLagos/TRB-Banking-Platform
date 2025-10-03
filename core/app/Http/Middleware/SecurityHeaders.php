<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Handle an incoming request and add security headers
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        
        // Content Security Policy
        $csp = [
            "default-src 'self'",
            "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com",
            "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdn.jsdelivr.net",
            "img-src 'self' data: https: blob:",
            "font-src 'self' https://fonts.gstatic.com",
            "connect-src 'self' https:",
            "media-src 'self'",
            "object-src 'none'",
            "child-src 'self'",
            "frame-ancestors 'none'",
            "base-uri 'self'",
            "form-action 'self'"
        ];
        
        // Security headers
        $headers = [
            // Content Security Policy
            'Content-Security-Policy' => implode('; ', $csp),
            
            // XSS Protection
            'X-XSS-Protection' => '1; mode=block',
            
            // Content Type Options
            'X-Content-Type-Options' => 'nosniff',
            
            // Frame Options
            'X-Frame-Options' => 'DENY',
            
            // Referrer Policy
            'Referrer-Policy' => 'strict-origin-when-cross-origin',
            
            // Strict Transport Security (HTTPS only)
            'Strict-Transport-Security' => 'max-age=31536000; includeSubDomains; preload',
            
            // Permissions Policy
            'Permissions-Policy' => 'camera=(), microphone=(), geolocation=(), payment=(), usb=()',
            
            // Remove server signature
            'Server' => 'WebServer',
            
            // Cache control for sensitive pages
            'Cache-Control' => $this->getCacheControlHeader($request),
            
            // Additional security headers
            'X-Permitted-Cross-Domain-Policies' => 'none',
            'Cross-Origin-Embedder-Policy' => 'require-corp',
            'Cross-Origin-Opener-Policy' => 'same-origin',
            'Cross-Origin-Resource-Policy' => 'same-origin',
        ];
        
        // Apply headers
        foreach ($headers as $key => $value) {
            $response->headers->set($key, $value);
        }
        
        // Remove potentially sensitive headers
        $response->headers->remove('X-Powered-By');
        $response->headers->remove('Server');
        
        return $response;
    }
    
    /**
     * Get appropriate cache control header based on request
     */
    private function getCacheControlHeader(Request $request): string
    {
        // Admin and sensitive routes should not be cached
        if ($request->is('admin*') || 
            $request->is('user/rebate*') || 
            $request->is('api/rebate*') ||
            $request->has('_token')) {
            return 'no-cache, no-store, must-revalidate, private';
        }
        
        // API routes can have short cache
        if ($request->is('api*')) {
            return 'public, max-age=300'; // 5 minutes
        }
        
        // Static assets can be cached longer
        if ($request->is('assets*') || 
            $request->is('images*') || 
            preg_match('/\.(css|js|png|jpg|jpeg|gif|svg|woff|woff2)$/', $request->path())) {
            return 'public, max-age=31536000'; // 1 year
        }
        
        // Default cache control
        return 'public, max-age=3600'; // 1 hour
    }
}