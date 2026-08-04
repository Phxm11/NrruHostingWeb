<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PermissionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  string|string[]  $permissions
     */
    public function handle(Request $request, Closure $next, string ...$permissions): Response
    {
        if (! auth()->check()) {
            return redirect()->route('login');
        }

        foreach ($permissions as $permission) {
            if (auth()->user()->hasPermission($permission)) {
                return $next($request);
            }
        }

        abort(403, 'คุณไม่มีสิทธิ์ดำเนินการนี้');
    }
}
