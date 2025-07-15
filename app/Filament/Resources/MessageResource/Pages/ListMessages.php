<?php

namespace App\Filament\Resources\MessageResource\Pages;

use App\Filament\Resources\MessageResource;
use App\Filament\Resources\ProductsResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;
use App\Models\Message;
use Illuminate\Database\Eloquent\Builder;

class ListMessages extends ListRecords
{
    protected static string $resource = MessageResource::class;
    protected static ?string $title = 'لیست پیام‌ها';
    protected static ?string $navigationIcon = 'heroicon-o-inbox';

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('id')
                ->label('شناسه')
                ->searchable(),
            TextColumn::make('type')
                ->label('نوع')
                ->searchable()
                ->formatStateUsing(fn ($state) => $state === 'sms' ? 'پیامک' : 'ایمیل'),
            TextColumn::make('recipients')
                ->label('گیرندگان')
                ->searchable()
                ->limit(50),
            BadgeColumn::make('status')
                ->label('وضعیت')
                ->colors([
                    'success' => 'sent',
                    'danger' => 'failed',
                ])
                ->searchable(),
            TextColumn::make('created_at')
                ->label('تاریخ ایجاد')
                ->dateTime('Y-m-d H:i')
                ->sortable(),
        ];
    }

    protected function getTableFilters(): array
    {
        return [
            SelectFilter::make('type')
                ->label('نوع پیام')
                ->options([
                    'sms' => 'پیامک',
                    'email' => 'ایمیل',
                ]),
            SelectFilter::make('status')
                ->label('وضعیت')
                ->options([
                    'sent' => 'ارسال‌شده',
                    'failed' => 'ناموفق',
                ]),
        ];
    }

    protected function getTableQuery(): Builder
    {
        return Message::query();
    }

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('createMessage')
                ->label('ارسال پیام جدید')
                ->url(fn () => route('filament.resources.messages.create'))
                ->icon('heroicon-o-plus')
                ->color('primary'),
        ];
    }
}
