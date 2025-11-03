<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class ThrottlePasswordReset
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $ip = $request->ip();
        $email = $request->input('email');
        $path = $request->path();

        // Determine the action type based on the route
        $action = 'general';
        if (str_contains($path, 'forgot-password')) {
            $action = 'forgot';
        } elseif (str_contains($path, 'verify-otp')) {
            $action = 'verify';
        } elseif (str_contains($path, 'reset-password')) {
            $action = 'reset';
        }

        // Rate limiting by IP: max 20 requests per hour (combined for all password reset actions)
        $ipKey = "password_reset_ip:{$ip}";
        $ipAttempts = Cache::get($ipKey, 0);

        if ($ipAttempts >= 20) {
            return response()->json([
                'success' => false,
                'message' => 'Too many password reset attempts from this IP. Please try again later.',
                'retry_after' => Cache::get("{$ipKey}_reset_time") - time()
            ], 429);
        }

        // Rate limiting by email with different limits per action
        if ($email) {
            $emailKey = "password_reset_email:{$email}:{$action}";
            $emailAttempts = Cache::get($emailKey, 0);

            // Different limits for different actions
            $limits = [
                'forgot' => 3,  // Max 3 OTP requests per hour
                'verify' => 10, // Max 10 OTP verification attempts per hour
                'reset' => 5,   // Max 5 password reset attempts per hour
            ];

            $limit = $limits[$action] ?? 5;

            if ($emailAttempts >= $limit) {
                $messages = [
                    'forgot' => 'Too many OTP requests for this email. Please try again later.',
                    'verify' => 'Too many OTP verification attempts for this email. Please try again later.',
                    'reset' => 'Too many password reset attempts for this email. Please try again later.',
                ];

                return response()->json([
                    'success' => false,
                    'message' => $messages[$action] ?? 'Too many password reset attempts for this email. Please try again later.',
                    'retry_after' => Cache::get("{$emailKey}_reset_time") - time()
                ], 429);
            }

            // Increment email attempts
            Cache::put($emailKey, $emailAttempts + 1, now()->addHour());
            Cache::put("{$emailKey}_reset_time", time() + 3600, now()->addHour());
        }

        // Increment IP attempts
        Cache::put($ipKey, $ipAttempts + 1, now()->addHour());
        Cache::put("{$ipKey}_reset_time", time() + 3600, now()->addHour());

        return $next($request);
    }
}
