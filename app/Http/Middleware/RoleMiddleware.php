<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, $role)
    {
        if (!auth()->check()) {
            return redirect('/login');
        }

        $user = auth()->user();

        if ($role !== 'all' && $user->role !== $role){
            abort(403, 'Unauthorized access');
        }

        return $next($request);
    }
}
