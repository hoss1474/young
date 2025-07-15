<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        return response()->json(Product::all());
    }


    public function showByPost()
    {
        $products = Product::all();

        $colorNames = \App\Models\Color::pluck('name', 'code')->toArray(); // code => name
        $baseUrl = config('app.url') . '/uploads/';
        $mapImage = fn($path) => $path ? $baseUrl . ltrim($path, '/') : null;

        $result = $products->map(function ($product) use ($colorNames, $mapImage) {
            $colorsRaw = is_array($product->colors) ? $product->colors : json_decode($product->colors, true) ?? [];
            $colors = collect($colorsRaw);

            $colorDetails = $colors->map(function ($item) use ($colorNames) {
                $code = $item['color'] ?? '';
                return [
                    'color_code' => $code,
                    'color_name' => $colorNames[$code] ?? 'نامشخص',
                    'stock' => $item['stock'] ?? 0,
                ];
            });

            return [
                'id' => $product->id,
                'name' => $product->name,
                'type' => $product->type,
                'price' => $product->price,
                'discount_price' => $product->discount_price,
                'description' => $product->description,
                'campaign' => optional($product->campaign)->only(['id', 'name', 'status']),
                'colors' => $colorDetails,
                'main_image' => $mapImage($product->main_image),
                'hover_image' => $mapImage($product->hover_image),
                'images' => array_values(array_filter([
                    $mapImage($product->image_1),
                    $mapImage($product->image_2),
                    $mapImage($product->image_3),
                    $mapImage($product->image_4),
                    $mapImage($product->image_5),
                    $mapImage($product->image_6),
                    $mapImage($product->image_7),
                    $mapImage($product->image_8),
                    $mapImage($product->image_9),
                    $mapImage($product->image_10),
                    $mapImage($product->image_11),
                    $mapImage($product->image_12),
                ])),
                'created_at' => $product->created_at->toDateTimeString(),
                'updated_at' => $product->updated_at->toDateTimeString(),
            ];
        });

        return response()->json([
            'is_status' => true,
            'statusCode' => 200,
            'message' => 'product list successful',
            'data' => $result
        ]);
    }


}
