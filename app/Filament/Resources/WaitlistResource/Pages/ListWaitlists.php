<?php

namespace App\Filament\Resources\WaitlistResource\Pages;

use App\Filament\Resources\WaitlistResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListWaitlists extends ListRecords
{
    protected static string $resource = WaitlistResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
