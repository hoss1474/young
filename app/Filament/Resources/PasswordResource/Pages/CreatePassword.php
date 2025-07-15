<?php

namespace App\Filament\Resources\PasswordResource\Pages;

use App\Filament\Resources\PasswordResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePassword extends CreateRecord
{
    protected static string $resource = PasswordResource::class;
}
