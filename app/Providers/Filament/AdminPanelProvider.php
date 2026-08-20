<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Help;
use App\Http\Middleware\SetLocaleMiddleware;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\MenuItem;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->brandName('DevCalc Quotation')
            ->font('Inter')
            ->login()
            ->colors([
                'primary' => Color::Blue,
            ])
            ->userMenuItems([
                MenuItem::make()
                    ->label(fn () => app()->getLocale() === 'id' ? '🇮🇩 Bahasa Indonesia (Aktif)' : '🇮🇩 Ganti ke Bahasa Indonesia')
                    ->url(fn (): string => route('language.switch', 'id'))
                    ->icon('heroicon-o-language'),
                MenuItem::make()
                    ->label(fn () => app()->getLocale() === 'en' ? '🇬🇧 English (Active)' : '🇬🇧 Switch to English')
                    ->url(fn (): string => route('language.switch', 'en'))
                    ->icon('heroicon-o-globe-alt'),
                'help' => MenuItem::make()
                    ->label(fn () => app()->getLocale() === 'id' ? 'Panduan Penggunaan' : 'User Guide & Help')
                    ->url(fn (): string => Help::getUrl())
                    ->icon('heroicon-o-question-mark-circle'),
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
                Help::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                // Widgets automatically discovered from app/Filament/Widgets
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
                SetLocaleMiddleware::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
