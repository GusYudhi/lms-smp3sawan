<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureGuruCanManageContent
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $permission ('berita' or 'prestasi')
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string $permission)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        $allowed = match ($permission) {
            'berita' => $user->canManageBerita(),
            'prestasi' => $user->canManagePrestasi(),
            default => false,
        };

        if (!$allowed) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengelola modul ini.');
        }

        return $next($request);
    }
}

