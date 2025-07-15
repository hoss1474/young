<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;
use App\Models\Client;
use App\Models\Waitlist;

use App\Models\Message;
use App\Models\Order;
use App\Models\Campaign; // اضافه کردن مدل Campaign
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PanelUsers extends BaseWidget
{
    protected function getCards(): array
    {
        try {
            $ClientCount = Client::count();
            $WaitlistCount = Waitlist::count();
//            $emailCount = Message::count(); // تعداد کل پیام‌ها بدون فیلتر type
            $latestSale = Order::latest()->first();
            $siteVisits = 0; // می‌تونی API اضافه کنی
            $campaignCount = Campaign::count(); // تعداد کمپین‌ها

            return [
                Card::make(' تعداد کاربران سایت', $ClientCount)
                    ->description('کل کاربران ثبت‌نام‌شده')
                    ->color('success'),
                Card::make('تعداد کاربران', $WaitlistCount)
                    ->description('کل کاربران لیست انتظار')
                    ->color('success'),
                Card::make('آخرین فروش', $latestSale ? $latestSale->created_at->format('Y-m-d H:i') : 'هیچ فروشی ثبت نشده')
                    ->description('تاریخ آخرین فروش')
                    ->color('warning'),
                Card::make('بازدید سایت', $siteVisits)
                    ->description('تعداد بازدیدهای سایت (داده موقت)')
                    ->color('primary'),
                Card::make('کمپین', $campaignCount) // اصلاح شده
                ->description('تعداد کمپین‌ها')
                    ->color('secondary'), // رنگ جدید برای تمایز
            ];
        } catch (\Exception $e) {
            return [
                Card::make('خطا', 0)
                    ->description('خطا در بارگذاری داده‌ها: ' . $e->getMessage())
                    ->color('danger'),
            ];
        }
    }
}
