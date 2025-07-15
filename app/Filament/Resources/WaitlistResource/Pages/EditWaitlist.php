<?php

namespace App\Filament\Resources\WaitlistResource\Pages;

use App\Filament\Resources\WaitlistResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditWaitlist extends EditRecord
{
    protected static string $resource = WaitlistResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
