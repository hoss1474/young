<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Address;

class AddressController extends Controller
{
// 📥 لیست آدرس‌های کلاینت لاگین‌شده
public function index()
{
$client = auth('api')->user();
return response()->json($client->addresses);
}

    public function store(Request $request)
    {
        $data = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'title' => 'required|string|max:100',
            'address' => 'required|string',
            'post_code' => 'required|string|max:20',
        ]);

        $address = \App\Models\Address::create($data);

        return response()->json([
            'message' => 'آدرس با موفقیت ثبت شد.',
            'address' => $address,
        ], 201);
    }


// ✏️ ویرایش آدرس
public function update(Request $request, $id)
{
$client = auth('api')->user();
$address = $client->addresses()->findOrFail($id);

$data = $request->validate([
'title' => 'nullable|string|max:100',
'address' => 'required|string',
'post_code' => 'nullable|string|max:20',
]);

$address->update($data);

return response()->json([
'message' => 'آدرس با موفقیت بروزرسانی شد.',
'address' => $address,
]);
}

// 🗑 حذف آدرس
public function destroy($id)
{
$client = auth('api')->user();
$address = $client->addresses()->findOrFail($id);
$address->delete();

return response()->json(['message' => 'آدرس حذف شد.']);
}
}
