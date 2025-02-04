<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CustomPermissionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next, $permission)
    {
        if (!Auth::user() || !Auth::user()->can($permission)) {
            return response()->json([
                'message' => 'Access denied. You do not have the necessary permissions to proceed.'
            ], 403);
        }

        return $next($request);
    }
}
