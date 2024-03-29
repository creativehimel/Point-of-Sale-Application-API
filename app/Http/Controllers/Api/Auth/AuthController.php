<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Mail\Auth\ForgetPasswordMail;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request):JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($request->only(['email', 'password']))) {
            return response()->json([
                'status' => 401,
                'message' => 'Invalid login details'
            ], 401);
        }

        $user = $request->user();
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

    public function register(Request $request):JsonResponse
    {
        $request->validate([
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'email' => 'required|string|email|max:100|unique:users,email',
            'password' => 'required|confirmed|string|min:6',
        ]);

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

    public function logout(Request $request):JsonResponse
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'status' => 200,
            'message' => 'Logged out successfully'
        ], 200);
    }

    public function forgetPassword(Request $request):JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->email)->first();
        if(!$user){
            return response()->json([
                'status' => 404,
                'message' => 'Account with this email not found.'
            ], 404);
        }

        $code = rand(111111, 999999);
        User::where('email', $request->email)->update(['otp' => $code]);
        $data = [
            'name' => $user->first_name . ' ' . $user->last_name,
            'code' => $code
        ];

        Mail::to($user->email)->send(new ForgetPasswordMail('Password Reset Code',$data));

        return response()->json([
            'status' => 200,
            'message' => 'Password reset code sent successfully'
        ]);
    }

    public function verifyCode(Request $request):JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|min:6'
        ]);

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json([
                'status' => 404,
                'message' => 'Account with this email not found.'
            ], 404);
        }
        if ($user->otp != $request->otp) {
            return response()->json([
                'status' => 401,
                'message' => 'Invalid code'
            ], 401);
        }

        User::where('email', $request->email)->update(['otp' => '0']);
        return response()->json([
            'status' => '200',
            'message' => 'OPT code verified successfully',
        ], 200);

    }

    public function resetPassword(Request $request):JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|confirmed|string|min:6',
        ]);

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json([
                'status' => 404,
                'message' => 'Account with this email not found.'
            ], 404);
        }
        User::where('email', $request->email)->update(['password' => bcrypt($request->password)]);
        return response()->json([
            'status' => 200,
            'message' => 'Password reset successfully'
        ], 200);
    }
}
