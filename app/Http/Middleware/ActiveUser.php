<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ActiveUser
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        abort_unless($user && $user->is_active && in_array($user->role, ['super_admin', 'admin_koperasi', 'admin_kwt']), 403);
        if ($user->role === 'admin_kwt') {
            abort_unless($user->kwt && $user->kwt->status === 'active', 403, 'KWT Anda tidak aktif.');
        }

        return $next($request);
    }
}
