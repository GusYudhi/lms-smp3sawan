<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!auth()->check()) {
            return redirect('/login');
        }

        $user = auth()->user();

        // Flatten roles array in case of comma separation
        $allowedRoles = [];
        foreach ($roles as $role) {
            // Explode by comma if role contains comma
            if (strpos($role, ',') !== false) {
                $parts = explode(',', $role);
                foreach ($parts as $part) {
                    $allowedRoles[] = trim($part);
                }
            } else {
                $allowedRoles[] = trim($role);
            }
        }

        // Check if any of the allowed roles matches 'all'
        if (in_array('all', $allowedRoles)) {
            return $next($request);
        }

        // Check if user's role is in the allowed roles list
        if (!in_array($user->role, $allowedRoles)) {
            abort(403, 'Unauthorized access');
        }

        return $next($request);
    }
}
