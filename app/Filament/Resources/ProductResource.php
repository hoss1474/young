<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use App\Models\Color;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Columns\ImageColumn;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;
    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?string $navigationLabel = 'محصولات';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('نام محصول')
                    ->required(),
                Forms\Components\TextInput::make('id')
                    ->label('کد محصول')
                    ->required(),

                Forms\Components\Select::make('type')
                    ->label('نوع')
                    ->options([
                        'drop' => 'drop',
                        'prodyct' => 'prodyct',
                    ])
                    ->required(),
                Forms\Components\TextColumn::make('price')
                    ->label('قیمت')
                    ->formatStateUsing(fn ($state) => number_format($state) . ' تومان')
                    ->sortable()
                    ->searchable(),

                Forms\Components\TextInput::make('discount_price')
                    ->label('قیمت با تخفیف')
                    ->numeric()
                    ->nullable(),
                Forms\Components\KeyValue::make('colors')
                    ->label('رنگ‌ها')
                    ->required(),
                Forms\Components\KeyValue::make('inventory')
                    ->label('موجودی')
                    ->required(),
                FileUpload::make('main_image')
                    ->label('تصویر اصلی')
                    ->disk('custom')
                    ->directory('products')
                    ->image()
                    ->required(),
                FileUpload::make('hover_image')
                    ->label('تصویر هاور')
                    ->disk('custom')
                    ->directory('products')
                    ->image()
                    ->nullable(),
                FileUpload::make('image_1')
                    ->label('تصویر 1')
                    ->disk('custom')
                    ->directory('products')
                    ->image()
                    ->nullable(),
                FileUpload::make('image_2')
                    ->label('تصویر 2')
                    ->disk('custom')
                    ->directory('products')
                    ->image()
                    ->nullable(),
                FileUpload::make('image_3')
                    ->label('تصویر 3')
                    ->disk('custom')
                    ->directory('products')
                    ->image()
                    ->nullable(),
                FileUpload::make('image_4')
                    ->label('تصویر 4')
                    ->disk('custom')
                    ->directory('products')
                    ->image()
                    ->nullable(),
                FileUpload::make('image_5')
                    ->label('تصویر 5')
                    ->disk('custom')
                    ->directory('products')
                    ->image()
                    ->nullable(),
                FileUpload::make('image_6')
                    ->label('تصویر 6')
                    ->disk('custom')
                    ->directory('products')
                    ->image()
                    ->nullable(),
                Forms\Components\Textarea::make('description')
                    ->label('توضیحات')
                    ->nullable(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('نام محصول'),
                Tables\Columns\TextColumn::make('id')->label('کد'),
                Tables\Columns\TextColumn::make('type')->label('نوع'),
                Tables\Columns\TextColumn::make('price')
                    ->label('قیمت')
                    ->formatStateUsing(fn ($state) => number_format($state) . ' تومان')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('discount_price')->label('تخفیف'),
                ImageColumn::make('main_image')
                    ->label('تصویر اصلی')
                    ->disk('public'),
            ])
            ->filters([
                //
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
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
