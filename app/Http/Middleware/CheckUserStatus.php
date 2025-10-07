<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && auth()->user()->status !== 'active') {
            // User is authenticated but inactive
            // Log them out and return error response
            auth()->logout();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Your account has been deactivated. Please contact administrator.',
                ], 403);
            }

            // For web routes, redirect to login with error message
            return redirect()->route('login')->with('error', 'Your account has been deactivated. Please contact administrator.');
        }

        return $next($request);
    }
}
