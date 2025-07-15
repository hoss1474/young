<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ColorResource\Pages;
use App\Models\Color;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ColorPicker;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ColorColumn;

class ColorResource extends Resource
{
    protected static ?string $model = Color::class;
//    protected static ?string $navigationIcon = 'heroicon-o-paint-brush';
    protected static ?string $navigationLabel = 'رنگ‌ها';
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('نام رنگ')
                    ->required()
                    ->maxLength(255),
                ColorPicker::make('code')
                    ->label('کد رنگ')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('شناسه')
                    ->searchable(),
                TextColumn::make('name')
                    ->label('نام رنگ')
                    ->searchable(),
                ColorColumn::make('code')
                    ->label('کد رنگ')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label('تاریخ ثبت')
                    ->dateTime(),
                TextColumn::make('updated_at')
                    ->label('آخرین تغییر')
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
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListColors::route('/'),
            'create' => Pages\CreateColor::route('/create'),
            'edit' => Pages\EditColor::route('/{record}/edit'),
        ];
    }
}
