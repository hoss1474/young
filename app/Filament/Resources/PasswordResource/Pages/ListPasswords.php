<?php

namespace App\Filament\Resources\PasswordResource\Pages;

use App\Filament\Resources\PasswordResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPasswords extends ListRecords
{
    protected static string $resource = PasswordResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
