<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditOrder extends EditRecord
{
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with('user');
    }

    protected static string $resource = OrderResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->before(function ($record) {
                    if (!$record) {
                        Notification::make()
                            ->title('خطا: رکورد پیدا نشد.')
                            ->danger()
                            ->send();
                        return false; // جلوگیری از ادامه اجرای Action
                    }
                }),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // چک کردن اینکه رکورد معتبر باشه
        if (!$this->record) {
            Notification::make()
                ->title('خطا: رکورد مورد نظر وجود ندارد.')
                ->danger()
                ->send();
            $this->halt();
        }
        return $data;
    }
}
