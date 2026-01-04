<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\PasswordResetOtp;
use App\Models\Role;
use App\Notifications\PasswordResetOtp as PasswordResetOtpNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register', 'forgotPassword', 'verifyOtp', 'resetPassword']]);
    }

    /**
     * Register a new user
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = JWTAuth::fromUser($user);

        return response()->json([
            'success' => true,
            'message' => 'User registered successfully',
            'user' => $user,
            'token' => $token
        ], 201);
    }

    /**
     * Login user and return token
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $credentials = $request->only('email', 'password');

        if (!$token = Auth::guard('api')->attempt($credentials)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials'
            ], 401);
        }

        $user = Auth::guard('api')->user();
        $roleActive = Role::with('permissions')->where('id', $user->activeRole()?->id ?? NULL)->first();

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => 'admin', // Default role for demo
                'avatar' => 'https://images.unsplash.com/photo-1568602471122-7832951cc4c5?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=facearea&facepad=2&w=320&h=320&q=80'
            ],
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::guard('api')->factory()->getTTL() * 60,
            'roles' => $user->roles,
            'role_active' => $roleActive,
        ]);
    }

    /**
     * Get authenticated user
     */
    public function me()
    {
        $user = Auth::guard('api')->user();
        $roleActive = Role::with('permissions')->where('id', $user->activeRole()?->id ?? NULL)->first();

        return response()->json([
            'success' => true,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => 'admin',
                'avatar' => 'https://images.unsplash.com/photo-1568602471122-7832951cc4c5?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=facearea&facepad=2&w=320&h=320&q=80',
            ],
            'roles' => $user->roles,
            'role_active' => $roleActive,
        ]);
    }

    /**
     * Logout user
     */
    public function logout()
    {
        Auth::guard('api')->logout();

        return response()->json([
            'success' => true,
            'message' => 'Successfully logged out'
        ]);
    }

    /**
     * Refresh token
     */
    public function refresh()
    {
        $token = Auth::guard('api')->refresh();

        return response()->json([
            'success' => true,
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::guard('api')->factory()->getTTL() * 60
        ]);
    }

    /**
     * Send password reset OTP
     */
    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $email = $request->email;

        // Check rate limiting (max 3 requests per hour per email)
        $recentRequests = PasswordResetOtp::where('email', $email)
            ->where('created_at', '>', now()->subHour())
            ->count();

        if ($recentRequests >= 3) {
            Log::warning('Password reset rate limit exceeded', [
                'email' => $email,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Too many password reset requests. Please try again later.'
            ], 429);
        }

        try {
            // Generate OTP
            $otpRecord = PasswordResetOtp::generateForEmail(
                $email,
                $request->ip(),
                $request->userAgent()
            );

            // Send OTP via email
            $this->sendOtpEmail($email, $otpRecord->getPlainOtp());

            Log::info('Password reset OTP sent', [
                'email' => $email,
                'ip' => $request->ip(),
                'otp_id' => $otpRecord->id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Password reset OTP has been sent to your email.',
                'expires_in' => 15 * 60 // 15 minutes in seconds
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send password reset OTP', [
                'email' => $email,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to send password reset email. Please try again.'
            ], 500);
        }
    }

    /**
     * Verify OTP
     */
    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'otp' => 'required|string|size:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $email = $request->email;
        $otp = $request->otp;

        // Find active OTP for this email
        $otpRecord = PasswordResetOtp::where('email', $email)
            ->active()
            ->first();

        if (!$otpRecord) {
            Log::warning('OTP verification failed - no active OTP', [
                'email' => $email,
                'ip' => $request->ip()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired OTP.'
            ], 400);
        }

        if ($otpRecord->verifyOtp($otp)) {
            Log::info('OTP verified successfully', [
                'email' => $email,
                'otp_id' => $otpRecord->id,
                'ip' => $request->ip()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'OTP verified successfully.',
                'token' => $otpRecord->id // Use OTP record ID as reset token
            ]);
        } else {
            Log::warning('OTP verification failed - invalid OTP', [
                'email' => $email,
                'otp_id' => $otpRecord->id,
                'attempts' => $otpRecord->attempts,
                'ip' => $request->ip()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Invalid OTP. Please check and try again.'
            ], 400);
        }
    }

    /**
     * Reset password
     */
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'token' => 'required|string', // OTP record ID
            'password' => 'required|string|min:8|confirmed|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $email = $request->email;
        $token = $request->token;
        $password = $request->password;

        // Find the used OTP record
        $otpRecord = PasswordResetOtp::where('id', $token)
            ->where('email', $email)
            ->whereNotNull('used_at')
            ->first();

        if (!$otpRecord || $otpRecord->isExpired()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired reset token.'
            ], 400);
        }

        try {
            // Update user password
            $user = User::where('email', $email)->first();
            $user->update([
                'password' => Hash::make($password)
            ]);

            // Clean up used OTPs for this email
            PasswordResetOtp::where('email', $email)->delete();

            Log::info('Password reset successful', [
                'email' => $email,
                'user_id' => $user->id,
                'ip' => $request->ip()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Password has been reset successfully.'
            ]);

        } catch (\Exception $e) {
            Log::error('Password reset failed', [
                'email' => $email,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to reset password. Please try again.'
            ], 500);
        }
    }

    /**
     * Send OTP via email
     */
    private function sendOtpEmail(string $email, string $otp)
    {
        // Create a temporary user object for notification
        $tempUser = (object) ['email' => $email];

        try {
            Notification::route('mail', $email)->notify(new PasswordResetOtpNotification($otp));

            Log::info('Password reset OTP notification sent', [
                'email' => $email,
                'otp_id' => null // We'll add this later if needed
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send password reset OTP notification', [
                'email' => $email,
                'error' => $e->getMessage()
            ]);

            // For development, log the OTP as fallback
            Log::info('Password Reset OTP (fallback)', [
                'email' => $email,
                'otp' => $otp,
                'expires_in' => '15 minutes'
            ]);
        }
    }
}
