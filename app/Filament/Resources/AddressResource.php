<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AddressResource\Pages;
use App\Models\Address;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;

class AddressResource extends Resource
{
    protected static ?string $model = Address::class;

    protected static ?string $navigationIcon = 'heroicon-o-location-marker';
    protected static ?string $navigationLabel = 'آدرس‌ها';
    protected static ?string $pluralModelLabel = 'آدرس‌ها';
    protected static ?string $modelLabel = 'آدرس';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('client_id')
                ->label('مشتری')
                ->relationship('client', 'first_name')
                ->searchable()
                ->required(),

            Forms\Components\TextInput::make('title')
                ->label('عنوان')
                ->placeholder('مثلاً خانه یا محل کار'),

            Forms\Components\TextInput::make('address')
                ->label('آدرس دقیق')
                ->required(),



            Forms\Components\TextInput::make('post_code')
                ->label('کد پستی'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('client.full_name')
                ->label('نام مشتری')
                ->searchable(),

            Tables\Columns\TextColumn::make('title')
                ->label('عنوان'),

            Tables\Columns\TextColumn::make('address')
                ->label('آدرس'),



            Tables\Columns\TextColumn::make('post_code')
                ->label('کد پستی'),
        ])
            ->filters([
                // اگر فیلتر خاصی خواستی بعداً اضافه کنیم
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
        return [
            // فعلاً relation خاصی نیاز نیست اینجا
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAddresses::route('/'),
            'create' => Pages\CreateAddress::route('/create'),
            'edit' => Pages\EditAddress::route('/{record}/edit'),
        ];
    }
}
