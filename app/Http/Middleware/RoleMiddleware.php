<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, $role = null)
    {
        // Ensure role parameter is provided
        if (!$role) {
            return response()->json(['error' => 'Role not specified in middleware'], 500);
        }
        
         // Ensure user is authenticated
        if (!$request->user() || !$request->user()->hasRole($role)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return $next($request);
    }
}
