<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Mail\ResetClientPasswordMail;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class ClientApiController extends Controller
{
    private function generateRandomPassword($length = 8)
    {
        return substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, $length);
    }

    private function generateResetCode($length = 6)
    {
        return substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, $length);
    }

    public function register(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:clients,email',
            'phone' => 'required|string|unique:clients,phone|max:20',
            'password' => 'required|string|min:8',
        ]);

        $client = Client::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'is_status' => true,
            'statusCode' => 200,
            'message' => 'ثبت‌نام با موفقیت انجام شد.',
        ]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $client = Client::where('email', $request->email)->first();

        if (!$client || !Hash::check($request->password, $client->password)) {
            return response()->json([
                'is_status' => false,
                'statusCode' => 422,
                'message' => 'نام کاربر یا گذر واژه اشتباه می باشد',
            ], 422);
        }

        $token = auth('api')->login($client);

        return $this->respondWithToken($token, 'ورود با موفقیت انجام شد');
    }

    public function refresh()
    {
        $newToken = auth('api')->refresh();
        return $this->respondWithToken($newToken, 'توکن با موفقیت تمدید شد');
    }

    public function me()
    {
        $client = auth('api')->user();

        return response()->json([
            'is_status' => true,
            'statusCode' => 200,
            'message' => 'اطلاعات کاربر',
            'data' => $client,
        ]);
    }

    public function logout()
    {
        auth('api')->logout();

        return response()->json([
            'is_status' => true,
            'statusCode' => 200,
            'message' => 'با موفقیت خارج شدید.',
        ]);
    }

    protected function respondWithToken($token, $message = 'توکن با موفقیت صادر شد')
    {
        $ttl = auth('api')->factory()->getTTL();
        $expiresIn = $ttl * 60;
        $expiresAt = time() + $expiresIn;

        return response()->json([
            'is_status' => true,
            'statusCode' => 200,
            'message' => $message,
            'data' => [
                'access_token' => $token,
                'refresh_token' => $token,
                'token_type' => 'bearer',
                'expires_in' => $expiresIn,
                'expires_at' => $expiresAt,
            ]
        ]);
    }

    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:clients,email',
        ]);

        $client = Client::where('email', $request->email)->first();

        $resetCode = $this->generateResetCode();
        $client->reset_code = $resetCode;
        $client->reset_code_expires_at = Carbon::now()->addMinutes(5); // 👈 اینجا درست ذخیره میشه
        $client->save();

        Mail::to($client->email)->send(new ResetClientPasswordMail($resetCode));

        return response()->json([
            'is_status' => true,
            'statusCode' => 200,
            'message' => 'کد تأیید به ایمیل شما ارسال شد.',
            'data' => [
                'id' => $client->id,
            ]
        ]);
    }


    public function changePassword(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'password' => 'required|string',
        ]);

        $client = Client::where('reset_code', $request->code)->first();

        if (!$client) {
            return response()->json([
                'is_status' => false,
                'statusCode' => 422,
                'message' => 'کد نامعتبر است.',
            ], 422);
        }

        if (
            !$client->reset_code_expires_at ||
            now()->gt($client->reset_code_expires_at)
        ) {
            return response()->json([
                'is_status' => false,
                'statusCode' => 422,
                'message' => 'کد منقضی شده است.',
            ], 422);
        }

        $client->password = Hash::make($request->password);
        $client->reset_code = null;
        $client->reset_code_expires_at = null;
        $client->save();

        return response()->json([
            'is_status' => true,
            'statusCode' => 200,
            'message' => 'رمز عبور با موفقیت تغییر کرد.',
        ]);
    }


}
