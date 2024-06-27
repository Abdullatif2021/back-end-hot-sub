<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!Auth::check()) return response()->json(['message' => 'Not authenticated'], 401);

        $user = Auth::user();
        if (!in_array($user->role, $roles)) {
            // Assuming 'role' is a property of your User model
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return $next($request);
    }
}
