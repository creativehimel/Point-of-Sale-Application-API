<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request):JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => $validator->messages(),
            ], 422);
        }else{
            if (!Auth::attempt($request->only(['email', 'password']))) {
                return response()->json([
                    'status' => 401,
                    'message' => 'Invalid login details'
                ], 401);
            }

            $user = $request->user()->select('id', 'email', 'first_name', 'last_name', 'password')->first();
            $user->tokens()->delete();
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'status' => 200,
                'message' => 'User logged in successfully',
                'data' => [
                    'token' => $token,
                    'user' => $user
                ]
            ], 200);
        }

    }

    public function register(Request $request):JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'email' => 'required|string|email|max:100|unique:users,email',
            'password' => 'required|confirmed|string|min:6',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => $validator->messages(),
            ], 422);
        }else{
            $user = User::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            if(!$user) {
                return response()->json([
                    'status' => 500,
                    'message' => 'Something went wrong'
                ], 500);
            }

            return response()->json([
                'status' => 201,
                'message' => 'User registered successfully'
            ], 201);
        }
    }

    public function logout(Request $request):JsonResponse
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'status' => 200,
            'message' => 'Logged out successfully'
        ], 200);
    }
}
