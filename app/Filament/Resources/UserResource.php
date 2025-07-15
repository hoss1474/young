<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\Pages\ListUsers;
use App\Filament\Resources\UserResource\Pages\CreateUser;
use App\Filament\Resources\UserResource\Pages\EditUser;
use App\Models\Client;
use App\Models\Address;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Support\Facades\Hash;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Builder;
class UserResource extends Resource
{
    protected static ?string $model = Client::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'کاربران';
    protected static ?string $pluralLabel = 'کاربران';
//    protected static ?string $navigationGroup = 'مدیریت کاربران';
    protected static ?string $slug = 'users';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('first_name')
                    ->label('نام')
//                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('last_name')
                    ->label('فامیلی')
//                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->label('ایمیل')
                    ->email()
//                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                Forms\Components\TextInput::make('phone')
                    ->label('شماره موبایل')
                    ->required()
                    ->maxLength(11)
                    ->rule('regex:/^09[0-9]{9}$/'),

                Forms\Components\TextInput::make('password')
                    ->label('رمز عبور (برای تغییر، مقدار وارد کنید)')
                    ->password()
                    ->dehydrateStateUsing(fn ($state) => filled($state) ? Hash::make($state) : null) // اگر پر بود هش کن
                    ->required(fn ($livewire) => $livewire instanceof \Filament\Resources\Pages\CreateRecord) // فقط در حالت ایجاد لازم است
                    ->maxLength(255),
                Forms\Components\TextInput::make('Addresses.address')
                    ->label('آدرس')
//                    ->required()
                    ->maxLength(500),
                FileUpload::make('profile_image')
                    ->label('تصویر پروفایل')
                    ->disk('public') // از فایل سیستم public استفاده می‌کنیم
                    ->directory('user-profiles') // مسیر دلخواه داخل storage/app/public/user-profiles/
                    ->image()
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/jpg', 'image/gif'])
                    ->maxSize(3048),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('first_name')
                    ->label('نام'),
                Tables\Columns\TextColumn::make('last_name')
                    ->label('فامیلی'),
                Tables\Columns\TextColumn::make('email')
                    ->label('ایمیل'),
                Tables\Columns\TextColumn::make('addresses.address')
                    ->label('آدرس')
                    ->searchable()
                    ->sortable(),

//                Tables\Columns\TextColumn::make('role')
//                    ->label('نقش'),
                Tables\Columns\TextColumn::make('phone')
                    ->label('شماره موبایل'),
//                Tables\Columns\TextColumn::make('code')
//                    ->label('کد '),
//                Tables\Columns\ImageColumn::make('profile_image')
//                    ->label('تصویر پروفایل')
//                    ->disk('public'),
//                Tables\Columns\TextColumn::make('created_at')
//                    ->label('تاریخ ایجاد')
//                    ->dateTime(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
