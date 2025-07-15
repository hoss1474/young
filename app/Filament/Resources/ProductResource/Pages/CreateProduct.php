<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use App\Models\Campaign;
use App\Models\Color;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;

    protected function getFormSchema(): array
    {
        return [
            TextInput::make('name')
                ->label('نام محصول')
                ->required()
                ->maxLength(255),



            Select::make('type')
                ->label('نوع محصول')
                ->options([
                    'drop' => 'drop',
                    'prodyct' => 'prodyct',

                ])
                ->required(),

            TextInput::make('price')
                ->label('قیمت')
                ->formatStateUsing(fn ($state) => number_format($state) . ' تومان')
                ->numeric()
                ->required(),

            TextInput::make('discount_price')
                ->label('قیمت با تخفیف ')
                ->formatStateUsing(fn ($state) => number_format($state) . ' تومان')
                ->numeric()
                ->nullable(),

            Select::make('campaign_id')
                ->label('کمپین')
                ->options(Campaign::pluck('name', 'id')->toArray())
                ->searchable()
                ->required(),

            Repeater::make('colors')
                ->label('رنگ‌ها و موجودی')
                ->schema([
                    Select::make('color')
                        ->label('رنگ')
                        ->options(Color::pluck('name', 'code')->toArray()) // یا 'id' به جای 'code' اگر بخوای آی‌دی ذخیره بشه
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
                ->directory('products')
                ->disk('custom')
                ->required()
                ->enableOpen()
                ->enableDownload()
                ->maxSize(2048),

            FileUpload::make('hover_image')
                ->label('عکس هاور')
                ->image()
                ->directory('products')
                ->disk('custom')
                ->nullable()
                ->enableOpen()
                ->enableDownload()
                ->maxSize(2048),

            Textarea::make('description')
                ->label('توضیحات کلی')
                ->nullable(),


            FileUpload::make('images')
                ->label('عکس‌های محصول')
                ->image()
                ->multiple()
                ->maxFiles(12)
                ->directory('products')
                ->disk('custom')
                ->enableOpen()
                ->enableDownload()
                ->maxSize(2048),
        ];
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // پردازش colors به‌صورت آرایه JSON
        if (isset($data['colors'])) {
            $colors = [];
            $inventory = [];

            foreach ($data['colors'] as $colorData) {
                $colors[] = [
                    'color' => $colorData['color'],
                    'stock' => (int)$colorData['stock'],
                ];
                $inventory[$colorData['color']] = (int)$colorData['stock'];
            }

            $data['colors'] = $colors;
            $data['inventory'] = $inventory;
        }

        // پردازش تصاویر چندگانه
        if (isset($data['images']) && is_array($data['images'])) {
            $imageFields = ['image_1', 'image_2', 'image_3', 'image_4', 'image_5', 'image_6', 'image_7', 'image_8', 'image_9', 'image_10', 'image_11', 'image_12'];
            foreach ($data['images'] as $index => $image) {
                if ($index < 12) {
                    $data[$imageFields[$index]] = $image;
                }
            }
            unset($data['images']);
        }

        return $data;
    }

}
