<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Otp;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class OtpController extends Controller
{
    public function sendOtp(Request $request)
    {
        $validated = $request->validate([
            'phone' => 'required|string|regex:/^09[0-9]{9}$/',
        ]);

        $phone = $validated['phone'];
        $otpCode = rand(100000, 999999);

        // ذخیره در دیتابیس
        Otp::updateOrCreate(
            ['phone' => $phone],
            [
                'otp_code' => $otpCode,
                'expires_at' => now()->addMinutes(5),
            ]
        );

        // ارسال به SMS.ir
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'x-api-key' => 'kebgPi3fvy7XqaeRKp7emhyljHBUiMKHhGRQnDJ3MfrVitYbsU6GqEUL6WawAalm',
        ])->post('https://api.sms.ir/v1/send/verify', [
            'mobile' => $phone,
            'templateId' => 526036,
            'parameters' => [
                [
                    'name' => 'code',
                    'value' => $otpCode
                ]
            ]
        ]);

        if ($response->successful()) {
            return response()->json(['message' => 'کد تایید ارسال شد']);
        } else {
            return response()->json(['message' => 'ارسال پیامک با خطا مواجه شد', 'details' => $response->json()], 500);
        }
    }

    public function showVerifyForm()
    {
        return view('auth.verify-otp');
    }

    public function verifyOtp(Request $request)
    {
        $validated = $request->validate([
            'otp_code' => ['required', 'string', 'size:6'],
        ]);

        $user = User::findOrFail(session('pending_user_id'));
        $otp = Otp::where('phone', $user->mobile)->first();

        if (! $otp) {
            return back()->withErrors(['otp' => 'کد تأیید یافت نشد.']);
        }

        if ($otp->expires_at < now()) {
            return back()->withErrors(['otp' => 'کد تأیید منقضی شده است.']);
        }

        if ($otp->otp_code !== $validated['otp_code']) {
            return back()->withErrors(['otp' => 'کد تأیید اشتباه است.']);
        }

        $otp->delete();
        Auth::guard('user')->login($user);
        session()->forget('pending_user_id');

        return redirect('/home');
    }
}
