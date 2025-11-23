<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiAuthMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // ✅ EXCLUDE PUBLIC ROUTES FROM AUTH
        $publicRoutes = [
            'api/staging/auth/*',
            'api/staging/applicants',
            'api/staging/scholar/submit_renewal', // ← ADD THIS
            'api/staging/debug/applicants',
        ];

        foreach ($publicRoutes as $publicRoute) {
            if ($request->is($publicRoute)) {
                return $next($request);
            }
        }

        $token = $request->bearerToken();
        
        if (!$token) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        try {
            // Decode the custom token
            $tokenData = json_decode(base64_decode($token), true);
            
            if (!$tokenData || !isset($tokenData['user_id']) || !isset($tokenData['user_type'])) {
                return response()->json(['message' => 'Invalid token.'], 401);
            }

            // Add user info to request
            $request->merge([
                'auth_user_id' => $tokenData['user_id'],
                'auth_user_type' => $tokenData['user_type']
            ]);

            return $next($request);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Invalid token format.'], 401);
        }
    }
}