<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use Filament\Resources\Pages\EditRecord;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use App\Models\Campaign;
use App\Models\Color;

class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;

    protected function getFormSchema(): array
    {
        return [
            TextInput::make('name')->label('نام محصول')->required(),

            TextInput::make('id')
                ->label('کد')
                ->required(),

            Select::make('type')
                ->label('نوع')
                ->options([
                    'drop' => 'drop',
                    'product' => 'product',

                ])
                ->required(),

            TextInput::make('price')
                ->label('قیمت')
                ->formatStateUsing(fn ($state) => number_format($state) . ' تومان')
                ->numeric()
                ->required(),

            TextInput::make('discount_price')
                ->label('قیمت با تخفیف')
                ->formatStateUsing(fn ($state) => number_format($state) . ' تومان')
                ->numeric()
                ->nullable(),

            Select::make('campaign_id')
                ->label('کمپین')
                ->options(function () {
                    return Campaign::pluck('name', 'id')->toArray();
                }),

            Repeater::make('colors')
                ->label('رنگ‌ها و موجودی')
                ->schema([
                    Select::make('color')
                        ->label('رنگ')
                        ->options(Color::pluck('name')->toArray()) // اگر می‌خوای id ذخیره شه
                        ->searchable()
                        ->required(),
                    TextInput::make('stock')
                        ->label('موجودی')
                        ->numeric()
                        ->required(),
                ])
                ->minItems(1)
                ->required(),

            FileUpload::make('main_image')
                ->label('عکس اصلی')
                ->image()
                ->disk('custom')
                ->directory('products')
                ->required(),

            FileUpload::make('hover_image')
                ->label('عکس هاور')
                ->image()
                ->disk('custom')
                ->directory('products')
                ->nullable(),

            Textarea::make('description')->label('توضیحات')->nullable(),


            FileUpload::make('image_1')->label('عکس 1')->image()->disk('custom')->directory('products')->nullable(),
            FileUpload::make('image_2')->label('عکس 2')->image()->disk('custom')->directory('products')->nullable(),
            FileUpload::make('image_3')->label('عکس 3')->image()->disk('custom')->directory('products')->nullable(),
            FileUpload::make('image_4')->label('عکس 4')->image()->disk('custom')->directory('products')->nullable(),
            FileUpload::make('image_5')->label('عکس 5')->image()->disk('custom')->directory('products')->nullable(),
            FileUpload::make('image_6')->label('عکس 6')->image()->disk('custom')->directory('products')->nullable(),
            FileUpload::make('image_7')->label('عکس 7')->image()->disk('custom')->directory('products')->nullable(),
            FileUpload::make('image_8')->label('عکس 8')->image()->disk('custom')->directory('products')->nullable(),
            FileUpload::make('image_9')->label('عکس 9')->image()->disk('custom')->directory('products')->nullable(),
            FileUpload::make('image_10')->label('عکس 10')->image()->disk('custom')->directory('products')->nullable(),
            FileUpload::make('image_11')->label('عکس 11')->image()->disk('custom')->directory('products')->nullable(),
            FileUpload::make('image_12')->label('عکس 12')->image()->disk('custom')->directory('products')->nullable(),
        ];
    }
}
