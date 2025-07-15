<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Client;

class UserApiController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:clients,email|max:255',
            'password' => 'required|string|min:8|max:255',
        ]);

        $client = Client::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'ثبت‌نام با موفقیت انجام شد.',
            'user' => $client,
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:8',
        ]);

        $client = Client::where('email', $request->email)->first();

        if (!$client || !Hash::check($request->password, $client->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'ایمیل یا رمز عبور اشتباه است.',
            ], 401);
        }

        $token = $client->createToken('client_token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'ورود با موفقیت انجام شد.',
            'token' => $token,
            'user' => $client,
        ], 200);
    }
}
