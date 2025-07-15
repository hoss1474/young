<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Illuminate\Support\Facades\Log;
use App\Models\Order;
use Illuminate\Database\Eloquent\Builder;

class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;

    protected function getTableQuery(): Builder
    {
        return Order::with('client');
    }

    protected function getTableActions(): array
    {
        return [
            EditAction::make(),
            DeleteAction::make()
                ->before(function ($record) {
                    if (!$record) {
                        Log::error('DeleteAction failed in ListOrders: Record is null', [
                            'url' => request()->url(),
                            'record_id' => request()->route('record') ?? 'not_found',
                        ]);
                        Notification::make()
                            ->title('خطا: رکورد پیدا نشد.')
                            ->danger()
                            ->send();
                        return false; // جلوگیری از ادامه اجرای Action
                    }
                }),
        ];
    }
}
