<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Password;
use Illuminate\Http\Request;


class PasswordApiController extends Controller
{

    public function check(Request $request)
    {
        $request->validate([
            'password' => 'required|string',
        ]);

        $passwordRow = Password::first();

        if (!$passwordRow) {
            return response()->json([
                'is_status' => false,
                'statusCode' => 404,
                'message' => 'هیچ رمزی در سیستم ثبت نشده است.',
            ], 404);
        }

        $match = $request->password === $passwordRow->password;

        return response()->json([
            'is_status' => $match,
            'statusCode' => $match ? 200 : 422,
            'message' => $match ? 'رمز صحیح است.' : 'رمز اشتباه است.',
        ], $match ? 200 : 422);
    }

}
