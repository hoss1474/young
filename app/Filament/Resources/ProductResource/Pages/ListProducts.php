<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\BadgeColumn;
use App\Models\Campaign
;
use App\Models\Color;

class ListProducts extends ListRecords
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Tables\Actions\CreateAction::make(),
        ];
    }

    protected function getTableColumns(): array
    {
        return [

            TextColumn::make('campaign_id')
                ->label('کمپین')
                ->searchable()
                ->sortable(),


            TextColumn::make('name')
                ->label('نام محصول')
                ->searchable()
                ->sortable(),

            TextColumn::make('id')
                ->label('کد محصول')
                ->searchable()
                ->sortable(),



            TextColumn::make('type')
                ->label(' نوع محصول')
                ->sortable(),


            TextColumn::make('price')
                ->label('قیمت')
                ->formatStateUsing(fn ($state) => number_format($state) . ' تومان')
                ->sortable()
                ->searchable(),




            TextColumn::make('stock_summary')
                ->label('موجودی بر اساس رنگ')
                ->getStateUsing(function ($record) {
                    $colors = $record->colors;

                    if (!is_array($colors)) return '-';

                    return collect($colors)
                        ->filter(fn ($item) => is_array($item) && isset($item['color'], $item['stock']))
                        ->map(function ($item) {
                            // پیدا کردن رنگ از جدول colors
                            $color = Color::where('code', $item['color'])->orWhere('id', $item['color'])->first();

                            if ($color) {
                                return "{$color->name} : {$item['stock']}";
                            } else {
                                return "{$item['color']}: {$item['stock']}";
                            }
                        })
                        ->implode(', ');
                }),
        ];
    }

    protected function getTableFilters(): array
    {
        return [


            SelectFilter::make('type')
                ->label('نوع')
                ->options([
                    'top' => 'بالاتنه',
                    'bottom' => 'پایین‌تنه',
                    'accessory' => 'اکسسوری',
                ]),
        ];
    }

    protected function getTableActions(): array
    {
        return [
            EditAction::make(),
            DeleteAction::make(),
        ];
    }
}
