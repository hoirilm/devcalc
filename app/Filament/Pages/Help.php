<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class Help extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-question-mark-circle';

    protected static ?int $navigationSort = 99;

    protected static string $view = 'filament.pages.help';

    public static function getNavigationGroup(): ?string
    {
        return app()->getLocale() === 'id' ? 'Bantuan & Dokumentasi' : 'Help & Support';
    }

    public static function getNavigationLabel(): string
    {
        return app()->getLocale() === 'id' ? 'Panduan Penggunaan' : 'User Guide & Help';
    }

    public function getTitle(): string
    {
        return app()->getLocale() === 'id' ? 'Panduan Penggunaan & Dokumentasi Sistem' : 'System Guide & Technical Documentation';
    }

    public static function shouldRegisterNavigation(): bool
    {
        return true;
    }
}
