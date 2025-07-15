<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PasswordResource\Pages;
use App\Filament\Resources\PasswordResource\RelationManagers;
use App\Models\Password;
use Filament\Forms;
use Filament\Forms\Components\Passwords;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Facades\Hash;
use Filament\Forms\Components\TextInput;

class PasswordResource extends Resource
{
    protected static ?string $model = Password::class;
    protected static ?string $navigationIcon = 'heroicon-o-lock-closed';
    protected static ?string $navigationLabel = '  پسورد';
    protected static ?int $navigationSort = 2;



    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('password')
                    ->label('رمز عبور')
                    ->required()

                   ,
            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('id'),
                TextColumn::make('password')
                    ->label('پسورد'),

//                TextColumn::make('updated_at')
//                    ->label('آخرین تغییر')
//                    ->dateTime(),
//                TextColumn::make('created_at')
//                    ->label('تاریخ ساخت ')
//                    ->dateTime(),
            ])
            ->filters([
                // فیلترها اینجا
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }


    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPasswords::route('/'),
            'create' => Pages\CreatePassword::route('/create'),
            'edit' => Pages\EditPassword::route('/{record}/edit'),
        ];
    }
}
