<?php

namespace App\Http\Middleware\Api;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApiAuthMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Check for Bearer token
        $token = $request->bearerToken();
        
        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication token required',
                'error' => 'MISSING_TOKEN'
            ], 401);
        }

        // Validate token and authenticate user
        try {
            $user = Auth::guard('api')->user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid authentication token',
                    'error' => 'INVALID_TOKEN'
                ], 401);
            }

            // Check if user account is active
            if ($user->status == 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Account is inactive',
                    'error' => 'ACCOUNT_INACTIVE'
                ], 403);
            }

            return $next($request);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication failed',
                'error' => 'AUTH_FAILED'
            ], 401);
        }
    }
}