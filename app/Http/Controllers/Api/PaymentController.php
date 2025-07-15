<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Color;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    public function pay(Request $request)
    {
        $request->validate([
            'campaign_id' => 'required|exists:campaigns,id',
            'address_id' => 'required|exists:addresses,id',
            'products' => 'required|array|min:1',
            'products.*.id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.color' => 'required|exists:colors,id',
        ]);

        $user = Auth::user();
        $total = 0;

        // ثبت سفارش
        $order = Order::create([
            'user_id' => $user->id,
            'campaign_id' => $request->campaign_id,
            'address_id' => $request->address_id,
            'tracking_code' => Str::random(10),
            'status' => 'pending',
            'price' => 0,
        ]);

        foreach ($request->products as $item) {
            $product = Product::find($item['id']);
            $total += $product->price * $item['quantity'];

            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item['id'],
                'color_id' => $item['color'],
                'quantity' => $item['quantity'],
                'price' => $product->price,
            ]);
        }

        $order->update(['price' => $total]);

        // ارسال به درگاه زرین‌پال
        $response = Http::post('https://api.zarinpal.com/pg/v4/payment/request.json', [
            'merchant_id' => env('ZARINPAL_MERCHANT_ID'),
            'amount' => $total,
            'callback_url' => route('zarinpal.callback'),
            'description' => 'پرداخت سفارش #' . $order->id,
            'metadata' => [
                'mobile' => $user->phone,
                'email' => $user->email,
            ],
        ]);

        $result = $response->json();

        if (isset($result['data']['code']) && $result['data']['code'] == 100) {
            $authority = $result['data']['authority'];
            $order->update(['gateway_authority' => $authority]);
            return response()->json([
                'is_status' => true,
                'checkout_url' => 'https://www.zarinpal.com/pg/StartPay/' . $authority,
            ]);
        } else {
            return response()->json([
                'is_status' => false,
                'message' => $result['errors']['message'] ?? 'خطا در اتصال به درگاه پرداخت.',
            ], 500);
        }
    }
}
