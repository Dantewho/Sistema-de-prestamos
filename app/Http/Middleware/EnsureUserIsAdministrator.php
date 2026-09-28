<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdministrator
{
    public function handle(Request $request, Closure $next): Response
    {
        abort_unless((int) $request->user()?->tipo_usuario === 1, 403, 'Solo los administradores pueden acceder al sistema.');

        return $next($request);
    }
}
