<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $permission): SymfonyResponse
    {
        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 401);
        }

        $user = auth()->user();

        if (!$user->hasPermission($permission)) {
            return response()->json([
                'success' => false,
                'message' => 'Forbidden: Insufficient permissions',
            ], 403);
        }

        return $next($request);
    }
}
