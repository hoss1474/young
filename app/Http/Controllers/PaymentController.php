<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Http;

class PaymentController extends Controller
{
    private $merchantID = '5ea2d1b4-fbf7-425d-a9fe-04755d943884'; // شناسه پذیرنده درگاه زرین پال

    // نمایش فرم پرداخت (مبلغ و اطلاعات کاربر)
    public function showPaymentForm()
    {
        return view('payment');
    }

    // ارسال درخواست پرداخت به زرین پال و ایجاد سفارش
    public function sendPayment(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1000',
            'email' => 'required|email',
            'name' => 'required|string|max:255',
        ]);

        // ساخت سفارش در دیتابیس
        $order = Order::create([
            'user_email' => $request->email,
            'amount' => $request->amount,
            'status' => 'pending',
            'user_name' => $request->name ?? null, // در صورت نیاز
        ]);

        // داده‌های ارسالی به زرین پال
        $data = [
            'MerchantID' => $this->merchantID,
            'Amount' => $order->amount,
            'Description' => "پرداخت سفارش شماره {$order->id}",
            'CallbackURL' => route('payment.callback', ['order' => $order->id]),
            'Metadata' => [
                'email' => $order->user_email,
                'mobile' => '',
            ],
        ];

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])->post('https://api.zarinpal.com/pg/v4/payment/request.json', $data);

        $responseData = $response->json();

        if (isset($responseData['data']['Code']) && $responseData['data']['Code'] == 100) {
            $order->authority = $responseData['data']['Authority'];
            $order->save();

            // ریدایرکت به درگاه پرداخت زرین پال
            return redirect('https://www.zarinpal.com/pg/StartPay/' . $order->authority);
        } else {
            $errorMessage = $responseData['errors'][0]['Message'] ?? 'خطای نامشخص در درگاه پرداخت';
            return back()->with('error', 'خطا در اتصال به درگاه پرداخت: ' . $errorMessage);
        }
    }

    // دریافت callback از زرین پال و تایید پرداخت
    public function callback(Request $request, Order $order)
    {
        $authority = $request->get('Authority');
        $status = $request->get('Status');

        if ($status == 'OK') {
            $data = [
                'MerchantID' => $this->merchantID,
                'Authority' => $authority,
                'Amount' => $order->amount,
            ];

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->post('https://api.zarinpal.com/pg/v4/payment/verify.json', $data);

            $responseData = $response->json();

            if (isset($responseData['data']['Code']) && $responseData['data']['Code'] == 100) {
                // پرداخت موفق
                $order->status = 'paid';
                $order->ref_id = $responseData['data']['RefID'] ?? null;
                $order->save();

                return redirect()->route('payment.success')->with('success', 'پرداخت با موفقیت انجام شد. کد پیگیری: ' . $order->ref_id);
            } else {
                $errorCode = $responseData['errors'][0]['Code'] ?? 'نامشخص';
                return redirect()->route('payment.fail')->with('error', 'پرداخت انجام نشد. کد خطا: ' . $errorCode);
            }
        } else {
            // پرداخت لغو یا ناموفق
            $order->status = 'canceled';
            $order->save();

            return redirect()->route('payment.fail')->with('error', 'پرداخت لغو شد یا ناموفق بود.');
        }
    }
}
