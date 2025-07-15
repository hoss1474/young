<?php

namespace App\Filament\Resources\MessageResource\Pages;

use App\Filament\Resources\MessageResource;
use Filament\Resources\Pages\Page;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Models\Message;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\ToArray;

class CreateMessage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string $resource = MessageResource::class;
    protected static string $view = 'filament.resources.message-resource.pages.create-message';
    protected static ?string $title = 'ارسال پیام';
    protected static ?string $navigationIcon = 'heroicon-o-paper-airplane';

    public array $formData = [];

    public function mount(): void
    {
        $this->form->fill([
            'SMS' => [
                'sms_recipients_list' => '',
                'sms_recipient_limit' => 'all',
                'sms_message' => '',
                'sms_recipients_file' => null,
            ],
            'Email' => [
                'email_recipients_list' => '',
                'email_recipient_limit' => 'all',
                'email_subject' => '',
                'email_template_file' => null,
                'email_recipients_file' => null,
            ],
        ]);
    }

    protected function getFormSchema(): array
    {
        return [
            \Filament\Forms\Components\Tabs::make('Message Type')
                ->statePath('formData')
                ->tabs([
                    \Filament\Forms\Components\Tabs\Tab::make('SMS')
                        ->label('پیامک')
                        ->schema([
                            \Filament\Forms\Components\Textarea::make('SMS.sms_recipients_list')
                                ->label('لیست شماره‌های موبایل')
                                ->helperText('شماره‌ها را با کاما یا خط جدید جدا کنید (مثال: 09123456789,09234567890)')
                                ->rows(3)
                                ->nullable(),
                            \Filament\Forms\Components\Select::make('SMS.sms_recipient_limit')
                                ->label('حداکثر گیرندگان')
                                ->options([
                                    '10' => '10 نفر',
                                    '50' => '50 نفر',
                                    '100' => '100 نفر',
                                    'all' => 'همه',
                                ])
                                ->default('all')
                                ->nullable(),
                            \Filament\Forms\Components\Textarea::make('SMS.sms_message')
                                ->label('متن پیامک')
                                ->maxLength(160)
                                ->hint('حداکثر 160 کاراکتر')
                                ->nullable(),
                            \Filament\Forms\Components\FileUpload::make('SMS.sms_recipients_file')
                                ->label('آپلود فایل اکسل شماره‌ها')
                                ->acceptedFileTypes(['application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'])
                                ->helperText('فایل اکسل باید شامل ستون "mobile" باشد (فرمت: .xls یا .xlsx)')
                                ->nullable()
                                ->disk('public')
                                ->directory('excel-uploads'),
                        ]),
                    \Filament\Forms\Components\Tabs\Tab::make('Email')
                        ->label('ایمیل')
                        ->schema([
                            \Filament\Forms\Components\Textarea::make('Email.email_recipients_list')
                                ->label('لیست ایمیل‌ها')
                                ->helperText('ایمیل‌ها را با کاما یا خط جدید جدا کنید (مثال: user1@example.com,user2@example.com)')
                                ->rows(3)
                                ->nullable()
                                ->disabled(fn ($get) => !empty($get('Email.email_recipients_file'))),
                            \Filament\Forms\Components\Select::make('Email.email_recipient_limit')
                                ->label('حداکثر گیرندگان')
                                ->options([
                                    '10' => '10 نفر',
                                    '50' => '50 نفر',
                                    '100' => '100 نفر',
                                    'all' => 'همه',
                                ])
                                ->default('all')
                                ->nullable(),
                            \Filament\Forms\Components\TextInput::make('Email.email_subject')
                                ->label('موضوع ایمیل')
                                ->maxLength(255)
                                ->nullable(),
                            \Filament\Forms\Components\FileUpload::make('Email.email_template_file')
                                ->label('آپلود فایل HTML')
                                ->acceptedFileTypes(['text/html'])
                                ->helperText('فقط فایل HTML آپلود کن (مثال: template.html). برای متغیر، {{ $userName }} رو توی فایل نگه دار.')
                                ->required()
                                ->disk('public')
                                ->directory('email-templates'),
                            \Filament\Forms\Components\FileUpload::make('Email.email_recipients_file')
                                ->label('آپلود فایل اکسل ایمیل‌ها')
                                ->acceptedFileTypes(['application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'])
                                ->helperText('فایل اکسل باید شامل ستون "email" باشد (فرمت: .xls یا .xlsx)')
                                ->nullable()
                                ->disk('public')
                                ->directory('excel-uploads'),
                        ]),
                ]),
        ];
    }

    public function submit()
    {
        try {
            $data = $this->form->getState()['formData'] ?? [];
            \Log::debug('Form state retrieved', ['data' => $data, 'type' => gettype($data)]);
        } catch (\Exception $e) {
            \Log::error('Error retrieving form state', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            Notification::make()
                ->title('خطا: مشکل در دریافت داده‌های فرم.')
                ->danger()
                ->send();
            return;
        }

        // چک نوع داده
        if (!is_array($data) || !isset($data['SMS']) || !isset($data['Email'])) {
            \Log::error('Invalid form data format', ['data' => $data, 'type' => gettype($data)]);
            Notification::make()
                ->title('خطا: داده‌های فرم نامعتبر است.')
                ->danger()
                ->send();
            return;
        }

        // بررسی اینکه حداقل یکی از تب‌ها پر شده باشد
        $smsTabFilled = !empty($data['SMS']['sms_recipients_list']) && !empty($data['SMS']['sms_message']);
        $emailTabFilled = (!empty($data['Email']['email_recipients_list']) || !empty($data['Email']['email_recipients_file'])) && !empty($data['Email']['email_template_file']);

        if (!$smsTabFilled && !$emailTabFilled) {
            Notification::make()
                ->title('خطا: حداقل یکی از تب‌های پیامک یا ایمیل باید پر شود.')
                ->danger()
                ->send();
            return;
        }

        // آماده‌سازی داده‌ها برای ذخیره
        $messageData = [
            'recipients' => '',
            'message' => '',
            'type' => '',
        ];

        // پردازش SMS
        $sms_success = 0;
        $sms_failed = 0;
        if ($smsTabFilled) {
            $sms_recipients = array_filter(array_map('trim', preg_split('/[\n,]+/', $data['SMS']['sms_recipients_list'])));
            $sms_limit = ($data['SMS']['sms_recipient_limit'] === 'all') ? count($sms_recipients) : (int) $data['SMS']['sms_recipient_limit'];
            $sms_recipients = array_slice($sms_recipients, 0, $sms_limit);

            foreach ($sms_recipients as $mobile) {
                if (preg_match('/^09[0-9]{9}$/', $mobile)) {
                    try {
                        $response = Http::withHeaders([
                            'x-api-key' => env('SMS_API_KEY'),
                            'Content-Type' => 'application/json',
                        ])->post('https://api.sms.ir/v1/send/verify', [
                            'mobile' => $mobile,
                            'templateId' => 526036,
                            'parameters' => [
                                ['name' => 'Message', 'value' => $data['SMS']['sms_message']],
                            ],
                        ]);
                        if ($response->successful()) {
                            $sms_success++;
                        } else {
                            $sms_failed++;
                            \Log::error('SMS sending failed', ['mobile' => $mobile, 'response' => $response->body()]);
                        }
                    } catch (\Exception $e) {
                        $sms_failed++;
                        \Log::error('SMS sending error', ['mobile' => $mobile, 'error' => $e->getMessage()]);
                    }
                } else {
                    $sms_failed++;
                }
            }

            // ذخیره داده‌های SMS
            $messageData['recipients'] = implode(', ', $sms_recipients);
            $messageData['message'] = $data['SMS']['sms_message'];
            $messageData['type'] = 'SMS';
        }

        // پردازش ایمیل
        $email_success = 0;
        $email_failed = 0;
        if ($emailTabFilled) {
            $email_recipients = [];

            // اگه فایل اکسل آپلود شده، ایمیل‌ها رو از فایل بخون
            if (!empty($data['Email']['email_recipients_file'])) {
                try {
                    $filePath = Storage::disk('public')->path($data['Email']['email_recipients_file']);
                    $rows = Excel::toArray(new class implements ToArray {
                        public function array(array $array)
                        {
                            return $array;
                        }
                    }, $filePath)[0];

                    // فرض می‌کنیم ستون "email" توی فایل وجود داره
                    $header = array_shift($rows); // ردیف اول هدره
                    $emailColumnIndex = array_search('email', $header);

                    if ($emailColumnIndex === false) {
                        \Log::error('Email column not found in Excel file');
                        Notification::make()
                            ->title('خطا: ستون "email" توی فایل اکسل پیدا نشد.')
                            ->danger()
                            ->send();
                        return;
                    }

                    foreach ($rows as $row) {
                        if (isset($row[$emailColumnIndex]) && filter_var($row[$emailColumnIndex], FILTER_VALIDATE_EMAIL)) {
                            $email_recipients[] = trim($row[$emailColumnIndex]);
                        }
                    }
                } catch (\Exception $e) {
                    \Log::error('Error reading Excel file', ['error' => $e->getMessage()]);
                    Notification::make()
                        ->title('خطا: مشکل در خواندن فایل اکسل.')
                        ->danger()
                        ->send();
                    return;
                }
            } else {
                // اگه فایل آپلود نشده، از لیست دستی استفاده کن
                $email_recipients = array_filter(array_map('trim', preg_split('/[\n,]+/', $data['Email']['email_recipients_list'])));
            }

            if (empty($email_recipients)) {
                Notification::make()
                    ->title('خطا: هیچ ایمیل معتبری پیدا نشد.')
                    ->danger()
                    ->send();
                return;
            }

            $email_limit = ($data['Email']['email_recipient_limit'] === 'all') ? count($email_recipients) : (int) $data['Email']['email_recipient_limit'];
            $email_recipients = array_slice($email_recipients, 0, $email_limit);

            $fromEmail = env('MAIL_FROM_ADDRESS', 'default@example.com');
            $fromName = env('MAIL_FROM_NAME', 'Default Name');

            if (empty($fromEmail) || !filter_var($fromEmail, FILTER_VALIDATE_EMAIL)) {
                \Log::error('Invalid or missing MAIL_FROM_ADDRESS', ['email' => $fromEmail]);
                Notification::make()
                    ->title('خطا: آدرس ایمیل فرستنده نامعتبر یا تنظیم نشده است.')
                    ->danger()
                    ->send();
                return;
            }

            if (empty($fromName) || !is_string($fromName)) {
                $fromName = 'Default Name';
                \Log::warning('MAIL_FROM_NAME is invalid or missing, using default', ['name' => $fromName]);
            }

            // خواندن محتوای فایل HTML
            $emailTemplateFile = $data['Email']['email_template_file'];
            if (!$emailTemplateFile || !Storage::disk('public')->exists($emailTemplateFile)) {
                \Log::error('Email template file not found', ['file' => $emailTemplateFile]);
                Notification::make()
                    ->title('خطا: فایل HTML پیدا نشد.')
                    ->danger()
                    ->send();
                return;
            }

            $emailBody = Storage::disk('public')->get($emailTemplateFile);

            foreach ($email_recipients as $email) {
                if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    try {
                        $userName = explode('@', $email)[0] ?? 'کاربر'; // اگه ایمیل خالی بود، پیش‌فرض "کاربر" می‌ذاره
                        $formattedEmailBody = str_replace('{{ $userName }}', $userName, $emailBody);

                        Mail::send([], [], function ($message) use ($email, $data, $fromEmail, $fromName, $formattedEmailBody) {
                            $message->to($email)
                                ->subject($data['Email']['email_subject'] ?? 'بدون موضوع')
                                ->from($fromEmail, $fromName)
                                ->html($formattedEmailBody);
                        });
                        $email_success++;
                    } catch (\Exception $e) {
                        $email_failed++;
                        \Log::error('Email sending failed', ['email' => $email, 'error' => $e->getMessage()]);
                    }
                } else {
                    $email_failed++;
                }
            }

            // ذخیره داده‌های ایمیل
            $messageData['recipients'] = implode(', ', $email_recipients);
            $messageData['message'] = $emailBody; // محتوای خام فایل HTML رو ذخیره می‌کنیم
            $messageData['type'] = 'Email';
        }

        // ذخیره پیام در دیتابیس
        try {
            Message::create($messageData);
        } catch (\Exception $e) {
            \Log::error('Failed to save message to database', ['error' => $e->getMessage()]);
            Notification::make()
                ->title('خطا: ذخیره پیام در دیتابیس با مشکل مواجه شد.')
                ->danger()
                ->send();
            return;
        }

        // اعلان نتیجه
        $message = '';
        if ($sms_success > 0 || $email_success > 0) {
            $message .= "ارسال موفق: $sms_success پیامک، $email_success ایمیل. ";
        }
        if ($sms_failed > 0 || $email_failed > 0) {
            $message .= "ارسال ناموفق: $sms_failed پیامک، $email_failed ایمیل.";
        }
        Notification::make()
            ->title($message ?: 'هیچ پیامی ارسال نشد.')
            ->status($sms_success + $email_success > 0 ? 'success' : 'danger')
            ->send();

        // ریست فرم
        $this->form->fill([
            'SMS' => [
                'sms_recipients_list' => '',
                'sms_recipient_limit' => 'all',
                'sms_message' => '',
                'sms_recipients_file' => null,
            ],
            'Email' => [
                'email_recipients_list' => '',
                'email_recipient_limit' => 'all',
                'email_subject' => '',
                'email_template_file' => null,
                'email_recipients_file' => null,
            ],
        ]);
    }
}
