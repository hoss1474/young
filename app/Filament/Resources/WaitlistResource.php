<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WaitlistResource\Pages;
use App\Models\Waitlist;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;

class WaitlistResource extends Resource
{
    protected static ?string $model = Waitlist::class;
    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationLabel = 'لیست انتظار';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('نام')
                    ->required()
                    ->maxLength(255),
                TextInput::make('email')
                    ->label('ایمیل')
                    ->email()
                    ->required()
                    ->unique(table: Waitlist::class, column: 'email')
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('شناسه'),
                TextColumn::make('full_name')
                    ->label('نام'),
                TextColumn::make('email')
                    ->label('ایمیل'),
                TextColumn::make('phone')
                    ->label('موبایل'),
                TextColumn::make('campaign_id')
                    ->label('کمپین'),
                TextColumn::make('created_at')
                    ->label('تاریخ ثبت')
                    ->dateTime(),

            ])
            ->filters([
                // فیلترها اینجا
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListWaitlists::route('/'),
            'create' => Pages\CreateWaitlist::route('/create'),
            'edit' => Pages\EditWaitlist::route('/{record}/edit'),
        ];
    }
}
