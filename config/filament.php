<?php

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Http\Middleware\MirrorConfigToSubpackages;
use Filament\Pages;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

return [

    /*
    |--------------------------------------------------------------------------
    | Filament Path
    |--------------------------------------------------------------------------
    */
    'path' => env('FILAMENT_PATH', 'admin'),

    /*
    |--------------------------------------------------------------------------
    | Filament Core Path
    |--------------------------------------------------------------------------
    */
    'core_path' => env('FILAMENT_CORE_PATH', 'filament'),

    /*
    |--------------------------------------------------------------------------
    | Filament Domain
    |--------------------------------------------------------------------------
    */
    'domain' => env('FILAMENT_DOMAIN'),

    /*
    |--------------------------------------------------------------------------
    | Homepage URL
    |--------------------------------------------------------------------------
    */
    'home_url' => '/',

    /*
    |--------------------------------------------------------------------------
    | Brand Name
    |--------------------------------------------------------------------------
    */
    'brand' => env('APP_NAME'),

    /*
    |--------------------------------------------------------------------------
    | Auth
    |--------------------------------------------------------------------------
    */
    'auth' => [
        'guard' => 'admin', // تغییر به گارد admin
        'pages' => [
            'login' => \Filament\Http\Livewire\Auth\Login::class,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Pages
    |--------------------------------------------------------------------------
    */
    'pages' => [
        'namespace' => 'App\\Filament\\Pages',
        'path' => app_path('Filament/Pages'),
        'register' => [
            Pages\Dashboard::class,
        ],
    ],

    /*
    |-------------------------------------------------------------------------
    | Resources
    |-------------------------------------------------------------------------
    */
    'resources' => [
        'namespace' => 'App\\Filament\\Resources',
        'path' => app_path('Filament/Resources'),
        'register' => [
            \App\Filament\Resources\AdminResource::class, // اضافه کردن AdminResource
        ],
    ],

    /*
    |-------------------------------------------------------------------------
    | Widgets
    |-------------------------------------------------------------------------
    */
    'widgets' => [
        'namespace' => 'App\\Filament\\Widgets',
        'path' => app_path('Filament/Widgets'),
        'register' => [
            Widgets\AccountWidget::class,
            Widgets\FilamentInfoWidget::class,
        ],
    ],

    /*
    |-------------------------------------------------------------------------
    | Livewire
    |-------------------------------------------------------------------------
    */
    'livewire' => [
        'namespace' => 'App\\Filament',
        'path' => app_path('Filament'),
    ],

    /*
    |-------------------------------------------------------------------------
    | Dark mode
    |-------------------------------------------------------------------------
    */
    'dark_mode' => false,

    /*
    |-------------------------------------------------------------------------
    | Database notifications
    |-------------------------------------------------------------------------
    */
    'database_notifications' => [
        'enabled' => false,
        'polling_interval' => '30s',
    ],

    /*
    |-------------------------------------------------------------------------
    | Broadcasting
    |-------------------------------------------------------------------------
    */
    'broadcasting' => [
        // 'echo' => [
        //     'broadcaster' => 'pusher',
        //     'key' => env('VITE_PUSHER_APP_KEY'),
        //     'cluster' => env('VITE_PUSHER_APP_CLUSTER'),
        //     'wsHost' => env('VITE_PUSHER_HOST'),
        //     'wsPort' => env('VITE_PUSHER_PORT'),
        //     'wssPort' => env('VITE_PUSHER_PORT'),
        //     'forceTLS' => true,
        // ],
    ],

    /*
    |-------------------------------------------------------------------------
    | Layout
    |-------------------------------------------------------------------------
    */
    'layout' => [
        'actions' => [
            'modal' => [
                'actions' => [
                    'alignment' => 'left',
                ],
            ],
        ],
        'forms' => [
            'actions' => [
                'alignment' => 'left',
                'are_sticky' => false,
            ],
            'have_inline_labels' => false,
        ],
        'footer' => [
            'should_show_logo' => true,
        ],
        'max_content_width' => null,
        'notifications' => [
            'vertical_alignment' => 'top',
            'alignment' => 'right',
        ],
        'sidebar' => [
            'is_collapsible_on_desktop' => false,
            'groups' => [
                'are_collapsible' => true,
            ],
            'width' => null,
            'collapsed_width' => null,
        ],
    ],

    /*
    |-------------------------------------------------------------------------
    | Favicon
    |-------------------------------------------------------------------------
    */
    'favicon' => null,

    /*
    |-------------------------------------------------------------------------
    | Default Avatar Provider
    |-------------------------------------------------------------------------
    */
    'default_avatar_provider' => \Filament\AvatarProviders\UiAvatarsProvider::class,

    /*
    |-------------------------------------------------------------------------
    | Default Filesystem Disk
    |-------------------------------------------------------------------------
    */
    'default_filesystem_disk' => env('FILAMENT_FILESYSTEM_DRIVER', 'public'),

    /*
    |-------------------------------------------------------------------------
    | Google Fonts
    |-------------------------------------------------------------------------
    */
    'google_fonts' => 'https://fonts.googleapis.com/css2?family=DM+Sans:ital,wght@0,400;0,500;0,700;1,400;1,500;1,700&display=swap',

    /*
    |-------------------------------------------------------------------------
    | Middleware
    |-------------------------------------------------------------------------
    */
    'middleware' => [
        'auth' => [
            \App\Http\Middleware\RestrictToAdmins::class, // اضافه کردن Middleware
            Authenticate::class,
        ],
        'base' => [
            EncryptCookies::class,
            AddQueuedCookiesToResponse::class,
            StartSession::class,
            AuthenticateSession::class,
            ShareErrorsFromSession::class,
            VerifyCsrfToken::class,
            SubstituteBindings::class,
            DispatchServingFilamentEvent::class,
            MirrorConfigToSubpackages::class,
        ],
    ],

];
