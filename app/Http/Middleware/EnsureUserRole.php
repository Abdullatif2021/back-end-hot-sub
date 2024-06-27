<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class EnsureUserRole
{
    public function handle(Request $request, Closure $next, $role)
    {
        if (Auth::check()) {
            $roles = explode('|', $role); // Split the roles
            foreach ($roles as $role) {
                if (Auth::user()->roles->contains('name', $role)) {
                    return $next($request); // User has one of the roles, proceed
                }
            }
        }
        
        // User does not have any of the required roles
        return response()->json([
            'success' => false,
            'message' => 'Unauthorized action.',
            'status' => 403
        ], 403);
    }
}
