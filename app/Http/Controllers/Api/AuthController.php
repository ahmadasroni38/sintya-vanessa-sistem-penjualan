<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
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
            'expires_in' => Auth::guard('api')->factory()->getTTL() * 60
        ]);
    }

    /**
     * Get authenticated user
     */
    public function me()
    {
        $user = Auth::guard('api')->user();

        return response()->json([
            'success' => true,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => 'admin',
                'avatar' => 'https://images.unsplash.com/photo-1568602471122-7832951cc4c5?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=facearea&facepad=2&w=320&h=320&q=80'
            ]
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
}