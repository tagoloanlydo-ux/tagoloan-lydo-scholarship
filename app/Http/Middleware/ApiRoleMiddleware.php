<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiRoleMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $userType = $request->get('auth_user_type');
        $userId = $request->get('auth_user_id');

        if (!$userType || !$userId) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        // Check role based on user type
        if ($role === 'scholar' && $userType !== 'scholar') {
            return response()->json(['message' => 'Access denied. Scholar role required.'], 403);
        }

        if ($role === 'lydo_admin' && $userType !== 'user') {
            return response()->json(['message' => 'Access denied. Admin role required.'], 403);
        }

        if ($role === 'lydo_staff' && $userType !== 'user') {
            return response()->json(['message' => 'Access denied. Staff role required.'], 403);
        }

        if ($role === 'mayor_staff' && $userType !== 'user') {
            return response()->json(['message' => 'Access denied. Mayor staff role required.'], 403);
        }

        // For user types, check specific roles in database
        if ($userType === 'user') {
            $user = \App\Models\User::find($userId);
            if (!$user) {
                return response()->json(['message' => 'User not found.'], 401);
            }

            if ($role === 'lydo_admin' && $user->role !== 'lydo_admin') {
                return response()->json(['message' => 'Access denied. Admin role required.'], 403);
            }

            if ($role === 'lydo_staff' && $user->role !== 'lydo_staff') {
                return response()->json(['message' => 'Access denied. Staff role required.'], 403);
            }

            if ($role === 'mayor_staff' && $user->role !== 'mayor_staff') {
                return response()->json(['message' => 'Access denied. Mayor staff role required.'], 403);
            }
        }

        return $next($request);
    }
}
