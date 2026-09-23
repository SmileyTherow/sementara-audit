<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ActiveUser
{
    public function handle(
        Request $request,
        Closure $next
    ): Response {
        $user = $request->user();

        abort_unless(
            $user
            && $user->is_active
            && (
                $user->isSuperAdmin()
                || $user->isOperatorKwt()
                || $user->isKoperasi()
            ),
            403
        );

        if ($user->isOperatorKwt()) {
            abort_unless(
                $user->kwt
                && $user->kwt->is_active,
                403,
                'KWT Anda tidak aktif.'
            );
        }

        return $next($request);
    }
}
