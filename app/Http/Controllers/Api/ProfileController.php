<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class ProfileController extends Controller
{
// 🧾 نمایش اطلاعات پروفایل کلاینت لاگین‌شده
    public function show()
    {
        $client = auth('api')->user();

        return response()->json([
            'first_name' => $client->first_name,
            'last_name' => $client->last_name,
            'email' => $client->email,
            'phone' => $client->phone,
            'password' => $client->password,
            'addresses' => $client->addresses->map(function ($address) {
                return [
                    'title' => $address->title,
                    'address' => $address->address,
                    'post_code' => $address->post_code,
                ];
            }),
            'avatar' => $client->avatar ? [[
                'alt' => 'avatar',
                'extension' => pathinfo($client->avatar, PATHINFO_EXTENSION),
                'data' => 'string',
                'name' => basename($client->avatar),
                'size' => 4256, // اگر اندازه واقعی فایل رو داشته باشی، جایگزین کن
                'type' => 'avatar',
                'uri' => asset('uploads/' . $client->avatar),
            ]] : [],
        ]);
    }



    public function userOrders()
    {
        try {
            $auth = getAuthenticatedClient(); // فرض: یک آبجکت با اطلاعات کلاینت لاگین‌شده
            $clientId = $auth->id;

            $orders = Order::where('user_id', $clientId)
                ->where('status', 'paid')
                ->select('id', 'tracking_code', 'address_id', 'price')
                ->with([
                    'address:id,client_id,title,address,post_code',
                    'orderItems:id,order_id,product_id,color_product_id,price,quantity',
                    'orderItems.colorProduct:id,product_id,color',
                    'orderItems.product:id,title',
                ])
                ->get();

            return ApiResponse::success("لیست سفارشات کاربر", ListUserOrdersResource::collection($orders));

        } catch (\Exception $exception) {
            \Log::error('ProfileController@userOrders', [
                'message' => $exception->getMessage(),
                'line' => $exception->getLine(),
            ]);

            return ApiResponse::failed(Response::HTTP_UNPROCESSABLE_ENTITY, 'خطا در دریافت سفارشات', (object)[]);
        }
    }


// ✏️ آپدیت اطلاعات پروفایل
public function update(Request $request)
{
$client = auth('api')->user();

$data = $request->validate([
'first_name' => 'nullable|string|max:100',
'last_name' => 'nullable|string|max:100',
'email' => 'nullable|email|unique:clients,email,' . $client->id,
'phone' => 'nullable|string|unique:clients,phone,' . $client->id,
'password' => 'nullable|string|min:6|confirmed',
]);

// اگر پسورد جدید ارسال شده، رمزنگاری کن
if (!empty($data['password'])) {
$data['password'] = Hash::make($data['password']);
} else {
unset($data['password']);
}

$client->update($data);

return response()->json([
'message' => 'پروفایل با موفقیت بروزرسانی شد.',
'client' => $client,
]);
}
}
