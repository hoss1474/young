<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MessageResource\Pages;
use App\Filament\Resources\MessageResource\Pages\ListMessages;
use App\Filament\Resources\MessageResource\Pages\CreateMessage;
use App\Models\Message;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;

class MessageResource extends Resource
{
    protected static ?string $model = Message::class;
    protected static ?string $navigationIcon = 'heroicon-o-mail';
    protected static ?string $navigationLabel = 'ارسال پیام';
    protected static ?string $pluralLabel = 'ارسال پیام‌ها';

    protected static ?string $slug = 'messages';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Message Type')
                    ->statePath('data')
                    ->tabs([
                        Tabs\Tab::make('SMS')
                            ->label('پیامک')
                            ->schema([
                                Textarea::make('sms_recipients_list')
                                    ->label('لیست شماره‌های موبایل')
                                    ->helperText('شماره‌ها را با کاما یا خط جدید جدا کنید (مثال: 09123456789,09234567890)')
                                    ->rows(3)
                                    ->nullable()
                                    ->validationAttribute('شماره‌های موبایل')
                                    ->rules([
                                        'nullable',
                                        'string',
                                        function ($attribute, $value, $fail) {
                                            if (empty($value)) {
                                                return;
                                            }
                                            $numbers = array_filter(array_map('trim', preg_split('/[\n,]+/', $value)));
                                            if (empty($numbers)) {
                                                $fail('حداقل یک شماره موبایل معتبر وارد کنید.');
                                            }
                                            foreach ($numbers as $number) {
                                                if (!preg_match('/^09\d{9}$/', $number)) {
                                                    $fail("شماره نامعتبر: $number");
                                                }
                                            }
                                        },
                                    ]),
                                FileUpload::make('sms_recipients_file')
                                    ->label('آپلود فایل اکسل شماره‌ها')
                                    ->acceptedFileTypes(['application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'])
                                    ->helperText('فایل اکسل باید شامل ستون "mobile" باشد (فرمت: .xls یا .xlsx)')
                                    ->nullable()
                                    ->disk('public')
                                    ->directory('excel-uploads')
                                    ->rules([
                                        'nullable',
                                        'file',
                                        'mimes:xls,xlsx',
                                    ]),
                                Select::make('sms_recipient_limit')
                                    ->label('حداکثر گیرندگان')
                                    ->options([
                                        '10' => '10 نفر',
                                        '50' => '50 نفر',
                                        '100' => '100 نفر',
                                        'all' => 'همه',
                                    ])
                                    ->default('all')
                                    ->nullable(),
                                Textarea::make('sms_message')
                                    ->label('متن پیامک')
                                    ->maxLength(160)
                                    ->hint('حداکثر 160 کاراکتر')
                                    ->nullable()
                                    ->rules([
                                        'nullable',
                                        'string',
                                        'max:160',
                                    ]),
                            ]),
                        Tabs\Tab::make('Email')
                            ->label('ایمیل')
                            ->schema([
                                Textarea::make('email_recipients_list')
                                    ->label('لیست ایمیل‌ها')
                                    ->helperText('ایمیل‌ها را با کاما یا خط جدید جدا کنید (مثال: user1@example.com,user2@example.com)')
                                    ->rows(3)
                                    ->nullable()
                                    ->validationAttribute('ایمیل‌ها')
                                    ->rules([
                                        'nullable',
                                        'string',
                                        function ($attribute, $value, $fail) {
                                            if (empty($value)) {
                                                return;
                                            }
                                            $emails = array_filter(array_map('trim', preg_split('/[\n,]+/', $value)));
                                            if (empty($emails)) {
                                                $fail('حداقل یک ایمیل معتبر وارد کنید.');
                                            }
                                            foreach ($emails as $email) {
                                                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                                                    $fail("ایمیل نامعتبر: $email");
                                                }
                                            }
                                        },
                                    ]),
                                FileUpload::make('email_recipients_file')
                                    ->label('آپلود فایل اکسل ایمیل‌ها')
                                    ->acceptedFileTypes(['application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'])
                                    ->helperText('فایل اکسل باید شامل ستون "email" باشد (فرمت: .xls یا .xlsx)')
                                    ->nullable()
                                    ->disk('public')
                                    ->directory('excel-uploads')
                                    ->rules([
                                        'nullable',
                                        'file',
                                        'mimes:xls,xlsx',
                                    ]),
                                FileUpload::make('email_template_file')
                                    ->label('آپلود فایل قالب HTML')
                                    ->acceptedFileTypes(['text/html'])
                                    ->helperText('فایل HTML برای قالب ایمیل (فرمت: .html)')
                                    ->nullable()
                                    ->disk('public')
                                    ->directory('email-templates')
                                    ->rules([
                                        'nullable',
                                        'file',
                                        'mimes:html',
                                    ]),
                                Select::make('email_recipient_limit')
                                    ->label('حداکثر گیرندگان')
                                    ->options([
                                        '10' => '10 نفر',
                                        '50' => '50 نفر',
                                        '100' => '100 نفر',
                                        'all' => 'همه',
                                    ])
                                    ->default('all')
                                    ->nullable(),
                                TextInput::make('email_subject')
                                    ->label('موضوع ایمیل')
                                    ->maxLength(255)
                                    ->nullable()
                                    ->rules([
                                        'nullable',
                                        'string',
                                        'max:255',
                                    ]),
                                Textarea::make('email_body')
                                    ->label('متن ایمیل (اختیاری)')
                                    ->maxLength(2000)
                                    ->nullable()
                                    ->rules([
                                        'nullable',
                                        'string',
                                        'max:2000',
                                    ])
                                    ->disabled(fn ($get) => !empty($get('email_template_file'))),
                            ]),
                    ]),
            ]);
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // آماده‌سازی داده‌ها برای ذخیره توی دیتابیس
        $messageData = [
            'recipients' => '',
            'message' => '',
            'type' => '',
        ];

        if (!empty($data['SMS']['sms_recipients_list'])) {
            $recipients = implode(', ', array_filter(array_map('trim', preg_split('/[\n,]+/', $data['SMS']['sms_recipients_list']))));
            $messageData['recipients'] = $recipients;
            $messageData['message'] = $data['SMS']['sms_message'];
            $messageData['type'] = 'SMS';
        } elseif (!empty($data['Email']['email_recipients_list'])) {
            $recipients = implode(', ', array_filter(array_map('trim', preg_split('/[\n,]+/', $data['Email']['email_recipients_list']))));
            $messageData['recipients'] = $recipients;
            $messageData['message'] = !empty($data['Email']['email_template_file'])
                ? file_get_contents(storage_path('app/public/' . $data['Email']['email_template_file']))
                : $data['Email']['email_body'];
            $messageData['type'] = 'Email';
        }

        return $messageData;
    }

    protected function afterSave(): void
    {
        Notification::make()
            ->title('پیام با موفقیت ثبت شد')
            ->success()
            ->send();
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('recipients')
                    ->label('گیرندگان')
                    ->searchable(),
                TextColumn::make('message')
                    ->label('متن پیام')
                    ->limit(50)
                    ->searchable(),
                TextColumn::make('type')
                    ->label('نوع پیام')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label('تاریخ ارسال')
                    ->dateTime('Y-m-d H:i'),
            ])
            ->filters([
                // می‌تونی فیلترها رو اینجا اضافه کنی
            ])
            ->actions([
                \Filament\Tables\Actions\EditAction::make(),
                \Filament\Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                \Filament\Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMessages::route('/'),
            'create' => Pages\CreateMessage::route('/create'),
        ];
    }
}
