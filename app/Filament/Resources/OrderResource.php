<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Models\Order;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Notifications\Actions\Action as NotificationAction;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ViewAction;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use App\Models\Address;
use App\Models\Client; // فراموش نشه
use Illuminate\Database\Eloquent\Builder;


class OrderResource extends Resource
{
    protected static ?string $model = Order::class;
    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?string $navigationLabel = 'لیست  سفارشات';
    protected static ?int $navigationSort = 1;

//    public static function form(Form $form): Form
//    {
//        return $form
//            ->schema([
//                Forms\Components\TextInput::make('user_id')
//                    ->label('شناسه کاربر')
//                    ->required(),
//                Forms\Components\TextInput::make('product_name')
//                    ->label('نام محصول')
//                    ->required(),
//                Forms\Components\TextInput::make('quantity')
//                    ->label('تعداد')
//                    ->numeric()
//                    ->required(),
//                Forms\Components\TextInput::make('price')
//                    ->label('قیمت')
//                    ->numeric()
//                    ->required(),
//                Forms\Components\TextInput::make('color')
//                    ->label('رنگ')
//                    ->required(),
//                Forms\Components\TextInput::make('tracking_code')
//                    ->label('کد رهگیری')
//                    ->default(fn () => Str::random(10))
//                    ->required(),
//                Forms\Components\TextInput::make('address_id')
//                    ->label('آدرس')
//                    ->required(),
//                Forms\Components\Select::make('status')
//                    ->label('وضعیت')
//                    ->options([
//                        'pending' => 'در انتظار',
//                        'processing' => 'در حال پردازش',
//                        'completed' => 'تکمیل‌شده',
//                        'canceled' => 'لغو‌شده',
//                    ])
//                    ->required(),
//            ]);
//    }
//

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('شناسه')
                    ->searchable()
                    ->url(fn ($record) => static::getUrl('view', ['record' => $record->id])),
                TextColumn::make('user_id')->label('شناسه کاربر'),
                TextColumn::make('client.first_name')->label('نام مشتری')->sortable()->searchable(),
                TextColumn::make('price')
                    ->label('قیمت')
                    ->formatStateUsing(fn ($state) => number_format($state) . ' تومان')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('tracking_code')->label('کد رهگیری')->searchable(),
                TextColumn::make('status')->label('وضعیت')->sortable(),
                TextColumn::make('address.address')
                    ->label('آدرس مشتری')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                // ...
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->before(function ($record) {
                        if (!$record) {
                            Log::error('DeleteAction failed: Record is null');
                            Notification::make()
                                ->title('خطا: رکورد پیدا نشد.')
                                ->danger()
                                ->send();
                            return false;
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()
                    ->before(function ($records) {
                        if ($records->isEmpty()) {
                            Log::error('DeleteBulkAction failed: No records found');
                            Notification::make()
                                ->title('خطا: هیچ رکوردی برای حذف پیدا نشد.')
                                ->danger()
                                ->send();
                            return false;
                        }
                    }),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
            'view' => Pages\ViewOrder::route('/{record}'),
        ];
    }

    public static function notifyUser($userId)
    {
        $user = User::find($userId);
        if ($user) {
            $trackingCode = Order::where('user_id', $userId)->latest()->value('tracking_code');
            $message = "سفارش شما با کد رهگیری {$trackingCode} ثبت شد. برای اطلاعات بیشتر به ایمیل خود مراجعه کنید.";

            // ارسال ایمیل
            Mail::raw($message, function ($message) use ($user) {
                $message->to($user->email)
                    ->subject('ثبت سفارش شما')
                    ->from(env('MAIL_FROM_ADDRESS', 'no-reply@example.com'), 'سایت شما');
            });

            // ارسال اس‌ام‌اس (فرض می‌کنیم API SMS داری)
            try {
                Http::withHeaders(['x-api-key' => env('SMS_API_KEY')])
                    ->post('https://api.sms.ir/v1/send/verify', [
                        'mobile' => $user->mobile,
                        'templateId' => 526036,
                        'parameters' => [['name' => 'Message', 'value' => $message]],
                    ]);
            } catch (\Exception $e) {
                Log::error('SMS sending failed', ['user_id' => $userId, 'error' => $e->getMessage()]);
            }

            Notification::make()
                ->title('اعلان ارسال شد')
                ->body("ایمیل و اس‌ام‌اس به کاربر {$user->name} ارسال شد.")
                ->success()
                ->send();
        } else {
            Notification::make()
                ->title('خطا')
                ->body('کاربر پیدا نشد.')
                ->danger()
                ->send();
        }
    }
}
