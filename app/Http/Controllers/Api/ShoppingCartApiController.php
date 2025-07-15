<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ShoppingCartApiController extends Controller
{
    public function shoppingCart(Request $request)
    {
        $request->validate([
            'campaign_id' => 'required|exists:campaigns,id',
            'address_id' => 'required|exists:addresses,id',
            'products' => 'required|array|min:1',
            'products.*.id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
        ]);

        $client = auth()->user();
        if (!$client) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $order = Order::create([
            'user_id' => $client->id,
            'campaign_id' => $request->campaign_id,
            'address_id' => $request->address_id,
            'status' => 'pending',
            'amount' => 0,
        ]);

        $totalAmount = 0;

        foreach ($request->products as $item) {
            $product = Product::findOrFail($item['id']);
            $quantity = $item['quantity'];
            $itemTotal = $product->price * $quantity;
            $totalAmount += $itemTotal;

            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'quantity' => $quantity,
                'price' => $product->price,
            ]);
        }

        $order->update(['amount' => $totalAmount]);

        $merchant_id = env('ZARINPAL_MERCHANT_ID', '5ea2d1b4-fbf7-425d-a9fe-04755d943884');
        $callback_url = route('zarinpal.callback', ['order' => $order->id]);

        $response = Http::post('https://api.zarinpal.com/pg/v4/payment/request.json', [
            'merchant_id' => $merchant_id,
            'amount' => $totalAmount,
            'description' => 'سفارش شما در سایت',
            'callback_url' => $callback_url,
        ]);

        $result = $response->json();

        if (isset($result['data']['code']) && $result['data']['code'] == 100) {
            $authority = $result['data']['authority'];
            $paymentUrl = "https://www.zarinpal.com/pg/StartPay/{$authority}";

            $order->update(['payment_token' => $authority]);

            return response()->json([
                'is_status' => true,
                'payment_url' => $paymentUrl,
                'message' => 'در حال انتقال به درگاه پرداخت...',
            ]);
        }

        return response()->json([
            'is_status' => false,
            'message' => 'خطا در اتصال به درگاه پرداخت.',
            'details' => $result,
        ], 500);
    }
}
