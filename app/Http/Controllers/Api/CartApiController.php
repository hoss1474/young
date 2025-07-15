<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartApiController extends Controller
{
    /**
     * Add a product to the cart.
     */
    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $user = Auth::user(); // فرض می‌کنیم کاربر لاگین کرده
        $product = Product::findOrFail($request->product_id);

        $cartItem = CartItem::firstOrCreate(
            [
                'user_id' => $user->id,
                'product_id' => $product->id,
            ],
            [
                'quantity' => $request->quantity,
                'price' => $product->price,
            ]
        );

        return response()->json([
            'message' => 'محصول به سبد خرید اضافه شد',
            'cart_item' => $cartItem,
        ], 201);
    }

    /**
     * View the cart contents.
     */
    public function index()
    {
        $user = Auth::user();
        $cartItems = CartItem::where('user_id', $user->id)->with('product')->get();

        return response()->json([
            'cart' => $cartItems,
        ], 200);
    }

    /**
     * Remove a product from the cart.
     */
    public function remove($id)
    {
        $user = Auth::user();
        $cartItem = CartItem::where('user_id', $user->id)->findOrFail($id);

        $cartItem->delete();

        return response()->json([
            'message' => 'محصول از سبد خرید حذف شد',
        ], 200);
    }

    /**
     * Checkout and create an order from the cart.
     */
    public function checkout()
    {
        $user = Auth::user();
        $cartItems = CartItem::where('user_id', $user->id)->with('product')->get();

        if ($cartItems->isEmpty()) {
            return response()->json([
                'message' => 'سبد خرید خالی است',
            ], 400);
        }

        // ایجاد سفارش جدید
        $order = Order::create([
            'user_id' => $user->id,
            'product_name' => $cartItems->first()->product->name, // برای سادگی، فقط نام اولین محصول
            'quantity' => $cartItems->sum('quantity'),
            'price' => $cartItems->sum(function ($item) {
                return $item->quantity * $item->price;
            }),
            'color' => 'نامشخص', // می‌تونی بعداً رنگ رو اضافه کنی
        ]);

        // خالی کردن سبد خرید
        CartItem::where('user_id', $user->id)->delete();

        return response()->json([
            'message' => 'سفارش با موفقیت ثبت شد',
            'order_id' => $order->id,
        ], 201);
    }
}
