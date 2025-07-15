<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Color;
use Illuminate\Http\Request;

class ColorApiController extends Controller
{
    public function add(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:colors,code',
        ]);

        $color = Color::create([
            'name' => $request->name,
            'code' => $request->code,
        ]);

        return response()->json([
            'is_status' => true,
            'statusCode' => 201,
            'message' => 'رنگ با موفقیت اضافه شد.',
            'data' => $color,
        ], 201);
    }

    public function check(Request $request)
    {
        $request->validate([
            'name' => 'nullable|string|max:255',
            'code' => 'nullable|string',
        ]);

        $query = Color::query();
        if ($request->name) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }
        if ($request->code) {
            $query->where('code', $request->code);
        }

        $colors = $query->get();

        if ($colors->isEmpty()) {
            return response()->json([
                'is_status' => false,
                'statusCode' => 404,
                'message' => 'هیچ رنگی یافت نشد.',
                'data' => [],
            ], 404);
        }

        return response()->json([
            'is_status' => true,
            'statusCode' => 200,
            'message' => 'لیست رنگ‌ها با موفقیت دریافت شد.',
            'data' => $colors,
        ], 200);
    }
}
