<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AdminResource\Pages;
use App\Models\Admin; // تغییر مدل به Admin
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;

class AdminResource extends Resource
{
    protected static ?string $model = Admin::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'کاربران پنل';
    protected static ?string $navigationGroup = 'مدیریت کاربران';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('نام')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('email')
                    ->label('ایمیل')
                    ->email()
                    ->required()
                    ->unique(table: Admin::class, column: 'email', ignorable: fn ($record) => $record),

                Forms\Components\TextInput::make('password')
                    ->label('رمز عبور')
                    ->password()
                    ->required(fn ($livewire) => $livewire instanceof Pages\CreateAdmin)
                    ->minLength(6)
                    ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                    ->visible(fn ($livewire) => $livewire instanceof Pages\CreateAdmin || $livewire instanceof Pages\EditAdmin),

                Forms\Components\Select::make('role')
                    ->label('نقش کاربر')
                    ->options([
                        'admin' => 'مدیر',

                    ])
                    ->default('admin')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('نام')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('ایمیل')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('role')
                    ->label('نقش')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاریخ ثبت‌نام')
                    ->formatStateUsing(fn ($state) => \Morilog\Jalali\Jalalian::fromDateTime($state)->format('Y/m/d H:i'))
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('role')
                    ->label('نقش')
                    ->options([
                        'admin' => 'مدیر',

                    ]),
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
            'index' => Pages\ListAdmins::route('/'),
            'create' => Pages\CreateAdmin::route('/create'),
            'edit' => Pages\EditAdmin::route('/{record}/edit'),
        ];
    }
}
