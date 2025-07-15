<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DateTimePicker;
use Mpdf\Mpdf;
use Filament\Forms\Components\Placeholder;
use Illuminate\Database\Eloquent\Builder;


class ViewOrder extends ViewRecord
{
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with('user');
    }

    protected static string $resource = OrderResource::class;

    protected function getActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\Action::make('send_notification')
                ->label('ارسال اعلان')
                ->color('success')
                ->icon('heroicon-o-paper-airplane')
                ->action(function () {
                    $order = $this->getRecord();
                    OrderResource::notifyUser($order->user_id);
                })
                ->requiresConfirmation(),
            Actions\Action::make('generate_invoice')
                ->label('خروجی فاکتور')
                ->color('primary')
                ->icon('heroicon-o-document-text')
                ->action(function () {
                    $order = $this->getRecord();
                    $user = $order->user;

                    // تنظیمات mPDF
                    $mpdf = new Mpdf([
                        'mode' => 'utf-8',
                        'format' => 'A5',
                        'orientation' => 'P',
                        'default_font' => 'shabnam',
                        'tempDir' => storage_path('app/mpdf_tmp'), // مسیر موقت برای mPDF
                        'useOTL' => 0xFF, // پشتیبانی از زبان‌های پیچیده مثل فارسی
                        'useKashida' => 25, // تنظیم کشیدگی برای فارسی
                        'autoLangToFont' => true, // انتخاب خودکار فونت برای زبان
                        'autoScriptToLang' => true, // شناسایی خودکار زبان
                    ]);

                    // رندر ویو به HTML
                    $html = view('invoices.order', compact('order', 'user'))->render();

                    // نوشتن HTML در PDF
                    $mpdf->WriteHTML($html);

                    // خروجی PDF به صورت دانلود
                    return response()->streamDownload(
                        fn () => print($mpdf->Output('', 'S')),
                        "invoice_{$order->id}.pdf",
                        ['Content-Type' => 'application/pdf']
                    );
                })
                ->requiresConfirmation(),
        ];
    }

    protected function getTitle(): string
    {
        return 'فاکتور سفارش';
    }

    protected function getHeaderWidgets(): array
    {
        return [];
    }

    protected function getFormSchema(): array
    {
        return [
            TextInput::make('product_name')
                ->label('نام محصول')
                ->disabled(),
            TextInput::make('quantity')
                ->label('تعداد')
                ->disabled(),
            TextInput::make('price')
                ->label('قیمت')
                ->disabled(),
            TextInput::make('color')
                ->label('رنگ')
                ->disabled(),
            TextInput::make('tracking_code')
                ->label('کدرهگیری')
                ->disabled(),
            TextInput::make('status')
                ->label('وضعیت')
                ->disabled(),
            Placeholder::make('user_address')
                ->label('آدرس')
                ->content(fn ($record) => $record->user?->address ?? '-'),
            Placeholder::make('user_name')
                ->label('نام کاربر')
                ->content(fn ($record) => $record->user?->name ?? '-'),
            Placeholder::make('user_email')
                ->label('ایمیل')
                ->content(fn ($record) => $record->user?->email ?? '-'),
            Placeholder::make('user_mobile')
                ->label('موبایل')
                ->content(fn ($record) => $record->user?->mobile ?? '-'),
            Placeholder::make('user_postal_code')
                ->label('کدپستی')
                ->content(fn ($record) => $record->user?->name ?? '-'),
            DateTimePicker::make('created_at')
                ->label('تاریخ سفارش')
                ->disabled()
                ->formatStateUsing(fn ($state) => \Morilog\Jalali\Jalalian::fromDateTime($state)->format('Y/m/d H:i'))
        ];
    }
}
