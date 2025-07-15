<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>فاکتور سفارش</title>
    <style>
        body {
            font-family: 'shabnam', sans-serif;
            direction: rtl !important;
            text-align: right !important;
            unicode-bidi: bidi-override; /* اطمینان از جهت‌گیری درست کاراکترها */
            margin: 0;
            padding: 0;
        }
        .invoice-box {
            width: 500px;
            margin: 20px auto;
            padding: 10px;
            border: 1px solid #ddd;
            direction: rtl !important;
        }
        h1 {
            font-size: 24px;
            text-align: right;
            margin-bottom: 20px;
        }
        h2 {
            font-size: 18px;
            text-align: right;
            margin: 15px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            direction: rtl !important;
            text-align: right;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: right;
            direction: rtl !important;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
            width: 35%;
        }
        td {
            width: 65%;
        }
        .order-table, .customer-table {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
<div class="invoice-box">
    <h1>فاکتور سفارش</h1>

    <h2>اطلاعات سفارش</h2>
    <table class="order-table">
        <tr>
            <th>عنوان</th>
            <th>مقدار</th>
        </tr>
        <tr>
            <td>شناسه سفارش</td>
            <td>{{ $order->id }}</td>
        </tr>
        <tr>
            <td>نام محصول</td>
            <td>{{ $order->product_name }}</td>
        </tr>
        <tr>
            <td>تعداد</td>
            <td>{{ $order->quantity }}</td>
        </tr>
        <tr>
            <td>قیمت</td>
            <td>{{ $order->price }} تومان</td>
        </tr>
        <tr>
            <td>رنگ</td>
            <td>{{ $order->color }}</td>
        </tr>
    <tr>
        <td>کدرهگیری</td>
        <td>{{ $order->tracking_code }}</td>
    </tr>
        <tr>
            <td>وضعیت</td>
            <td>{{ $order->status }}</td>
        </tr>
        <tr>
            <td>تاریخ سفارش</td>
            <td>{{ $order->created_at->format('Y-m-d H:i') }}</td>
        </tr>
    </table>

    <h2>اطلاعات مشتری</h2>
    <table class="customer-table">
        <tr>
            <th>عنوان</th>
            <th>مقدار</th>
        </tr>
        <tr>
            <td>نام</td>
            <td>{{ $user->name }}</td>
        </tr>
        <tr>
            <td>ایمیل</td>
            <td>{{ $user->email }}</td>
        </tr>
        <tr>
            <td>شماره تلفن</td>
            <td>{{ $user->mobile }}</td>
        </tr>
        <tr>
            <td>آدرس</td>
            <td>{{ $user->address }}</td>
        </tr>
        <tr>
            <td>کد پستی</td>
            <td>{{ $user->postal_code }}</td>
        </tr>
    </table>
</div>
</body>
</html>
