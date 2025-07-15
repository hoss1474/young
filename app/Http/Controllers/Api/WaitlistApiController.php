<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Waitlist;
use Illuminate\Http\Request;

class WaitlistApiController extends Controller
{
    public function check(Request $request)
    {
        $request->validate([
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:20',
        ]);

        // اگر ایمیل یا شماره تلفن وارد شده، فیلتر کن
        $query = Waitlist::query();
        if ($request->email) {
            $query->where('email', $request->email);
        }
        if ($request->phone) {
            $query->where('phone', $request->phone);
        }

        $waitlistRows = $query->get();

        if ($waitlistRows->isEmpty()) {
            return response()->json([
                'is_status' => false,
                'statusCode' => 404,
                'message' => 'هیچ رکوردی در لیست انتظار یافت نشد.',
                'data' => [],
            ], 422);
        }

        return response()->json([
            'is_status' => true,
            'statusCode' => 200,
            'message' => 'لیست انتظار با موفقیت دریافت شد.',
            'data' => $waitlistRows,
        ], 200);
    }

    // متد add بدون تغییر
    public function add(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|unique:waitlists,email',
            'phone' => 'required|string|max:255',
            'campaign_id' => 'required|string|max:255',
        ]);

        $waitlist = Waitlist::create([
            'full_name' => $request->full_name,
            'email' => $request->email,
            'phone' =>  $request->phone,

        ]);

        return response()->json([
            'is_status' => true,
            'statusCode' => 200,
            'message' => 'کاربر با موفقیت به لیست انتظار اضافه شد.',
            'data' => $waitlist,
        ], 200);
    }
}
