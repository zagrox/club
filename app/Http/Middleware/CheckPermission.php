<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$permissions): Response
    {
        if (!$request->user()) {
            return redirect()->route('login');
        }

        // Check if the user has any of the required permissions
        foreach ($permissions as $permission) {
            if ($request->user()->hasPermissionTo($permission)) {
                return $next($request);
            }
        }

        abort(403, 'You do not have permission to access this resource.');
    }
} 