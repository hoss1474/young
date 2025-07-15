<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateOrder extends CreateRecord
{
    protected static string $resource = OrderResource::class;

    protected function getActions(): array
    {
        return [
            Actions\Action::make('cancel')
                ->label('لغو')
                ->url(fn () => $this->getResource()::getUrl('index'))
                ->color('secondary'),
        ];
    }

    protected function afterCreate(): void
    {
        // بعد از ایجاد سفارش، به صفحه لیست هدایت می‌شه
        $this->redirect($this->getResource()::getUrl('index'));
    }
}
