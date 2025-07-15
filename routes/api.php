<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use App\Models\User;
use App\Http\Controllers\Api\UserApiController;
use App\Http\Controllers\OtpController;
use App\Http\Controllers\Api\OrderApiController;
use App\Http\Controllers\Api\CartApiController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\PostApiController;
use App\Http\Controllers\CampaignApiController;
use App\Http\Controllers\Api\ClientApiController;
use App\Http\Controllers\Api\PasswordApiController;
use App\Http\Controllers\Api\WaitlistApiController;
use App\Http\Controllers\Api\AddressController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\ShoppingCartApiController;
use App\Http\Controllers\Api\PaymentController;



/*
|--------------------------------------------------------------------------
| Public Routes (No Auth Required)
|--------------------------------------------------------------------------
*/

// Register
Route::post('/register', [ClientApiController::class, 'register']);
// Login
Route::post('/login', [ClientApiController::class, 'login']);
Route::post('/send-otp-code', [ClientApiController::class, 'forgotPassword']);
Route::post('/change-password', [ClientApiController::class, 'changePassword']);



Route::post('/check-password', [PasswordApiController::class, 'check']);




Route::post('/request-waitlist', [WaitlistApiController::class, 'add']);
Route::post('/list-waitlist', [WaitlistApiController::class, 'check']);




// Product Routes
Route::post('/details-product', [ProductController::class, 'detailByPost']);
Route::post('/products', [ProductController::class, 'showByPost']);



Route::middleware('auth:api')->group(function () {
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::put('/update-profile', [ProfileController::class, 'update']);
    Route::get('/user-orders', [ProfileController::class, 'userOrders']);
});




Route::middleware('auth:api')->group(function () {
    Route::post('/create-address', [AddressController::class, 'store']);
});


Route::middleware('auth:api')->group(function () {
    Route::post('/payment/pay', [PaymentController::class, 'pay']);
});



Route::post('/shoppingCart', [ShoppingCartApiController::class, 'shoppingCart']);

