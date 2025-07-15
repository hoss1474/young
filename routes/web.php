<?php

use App\Filament\Resources\OrderResource;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\OtpController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ShoppingCartApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Order;



/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');





Route::get('/checkout/success', [ShoppingCartApiController::class, 'checkoutSuccess'])->name('checkout.success');
Route::get('/checkout/cancel', [ShoppingCartApiController::class, 'checkoutCancel'])->name('checkout.cancel');





Route::get('/campaign/create', [CampaignController::class, 'create'])->name('create.campaign');
Route::post('/campaign/store', [CampaignController::class, 'store'])->name('store.campaign');


Route::get('/payment/callback', function (Request $request) {
    $authority = $request->get('Authority');
    $status = $request->get('Status');

    if ($status !== 'OK') {
        return 'پرداخت لغو شد یا ناموفق بود.';
    }

    $order = Order::where('gateway_authority', $authority)->first();

    if (!$order) {
        return 'سفارش پیدا نشد.';
    }

    $response = Http::post('https://api.zarinpal.com/pg/v4/payment/verify.json', [
        'merchant_id' => env('ZARINPAL_MERCHANT_ID'),
        'amount' => $order->amount,
        'authority' => $authority,
    ]);

    $result = $response->json();

    if (isset($result['data']['code']) && $result['data']['code'] == 100) {
        $order->update([
            'status' => 'completed',
            'ref_id' => $result['data']['ref_id'] ?? null,
        ]);

        return '✅ پرداخت با موفقیت انجام شد. شماره پیگیری: ' . $result['data']['ref_id'];
    } else {
        $order->update(['status' => 'failed']);
        return '❌ پرداخت ناموفق بود یا تأیید نشد.';
    }
})->name('zarinpal.callback');

