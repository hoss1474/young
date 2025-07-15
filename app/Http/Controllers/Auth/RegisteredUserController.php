<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Controllers\OtpController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class RegisteredUserController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'mobile' => ['required', 'string', 'regex:/^09[0-9]{9}$/', 'unique:users,mobile'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'mobile' => $validated['mobile'],
            'password' => Hash::make($validated['password']),
            'role' => 'customer',
        ]);

        // ارسال OTP با استفاده از OtpController
        $otpController = new OtpController();
        $otpController->sendOtp(new Request(['phone' => $user->mobile]));

        session(['pending_user_id' => $user->id]);

        return redirect()->route('otp.verify');
    }
}
