<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Mpdf\Mpdf;
use Illuminate\Support\Facades\Response;
use Carbon\Carbon;

class OrderApiController extends Controller
{
    /**
     * Display the specified order.
     */
    public function show($id)
    {
        $order = Order::with('user')->findOrFail($id);

        return response()->json([
            'order' => [
                'id' => $order->id,
                'product_name' => $order->product_name,
                'quantity' => $order->quantity,
                'price' => $order->price,
                'color' => $order->color,
                'created_at' => $order->created_at->format('Y-m-d H:i'),
            ],
            'user' => [
                'name' => $order->user->name ?? 'نامشخص',
                'email' => $order->user->email ?? 'نامشخص',
                'mobile' => $order->user->mobile ?? 'نامشخص',
                'address' => $order->user->address ?? 'نامشخص',
                'postal_code' => $order->user->postal_code ?? 'نامشخص',
            ],
        ], 200);
    }

    /**
     * Generate and download the invoice PDF for the specified order.
     */
    public function generateInvoice($id)
    {
        $order = Order::with('user')->findOrFail($id);
        $user = $order->user;

        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'orientation' => 'P',
            'default_font' => 'vazir',
            'tempDir' => storage_path('app/mpdf_tmp'),
            'useOTL' => 0xFF,
            'useKashida' => 25,
            'autoLangToFont' => true,
            'autoScriptToLang' => true,
        ]);

        $html = view('invoices.order', compact('order', 'user'))->render();
        $mpdf->WriteHTML($html);

        return Response::streamDownload(
            fn () => print($mpdf->Output('', 'S')),
            "invoice_{$order->id}.pdf",
            ['Content-Type' => 'application/pdf']
        );
    }

    /**
     * Get the latest products.
     */
    public function latestProducts()
    {
        $products = Product::orderBy('created_at', 'desc')->take(5)->get(['id', 'name', 'price', 'discount']);

        return response()->json([
            'latest_products' => $products,
        ], 200);
    }

    /**
     * Get daily discounts.
     */
    public function dailyDiscounts()
    {
        $today = Carbon::today();
        $products = Product::where('discount', '>', 0)
            ->whereDate('discount_date', $today)
            ->get(['id', 'name', 'price', 'discount']);

        return response()->json([
            'daily_discounts' => $products,
        ], 200);
    }

    /**
     * Get most popular products.
     */
    public function popularProducts()
    {
        $products = Product::leftJoin('orders', 'products.id', '=', 'orders.product_id')
            ->select('products.id', 'products.name', 'products.price', 'products.discount', \DB::raw('COUNT(orders.id) as order_count'))
            ->groupBy('products.id', 'products.name', 'products.price', 'products.discount')
            ->orderBy('order_count', 'desc')
            ->take(5)
            ->get();

        return response()->json([
            'popular_products' => $products,
        ], 200);
    }

    /**
     * Get products with the highest discount.
     */
    public function highestDiscounts()
    {
        $products = Product::where('discount', '>', 0)
            ->orderBy('discount', 'desc')
            ->take(5)
            ->get(['id', 'name', 'price', 'discount']);

        return response()->json([
            'highest_discounts' => $products,
        ], 200);
    }
}
