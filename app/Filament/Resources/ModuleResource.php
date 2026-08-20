<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ModuleResource\Pages;
use App\Models\Module;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ModuleResource extends Resource
{
    protected static ?string $model = Module::class;

    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';

    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): ?string
    {
        return app()->getLocale() === 'id' ? 'Data Master' : 'Master Data';
    }

    public static function getNavigationLabel(): string
    {
        return app()->getLocale() === 'id' ? 'Katalog Modul' : 'Feature Modules';
    }

    public static function getModelLabel(): string
    {
        return app()->getLocale() === 'id' ? 'Modul Fitur' : 'Feature Module';
    }

    public static function getPluralModelLabel(): string
    {
        return app()->getLocale() === 'id' ? 'Katalog Modul' : 'Feature Modules';
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->hasRole('Admin') ?? false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->hasRole('Admin') ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(app()->getLocale() === 'id' ? 'Detail Modul Fitur' : 'Feature Module Details')
                    ->description(app()->getLocale() === 'id' ? 'Kelola katalog fitur standar software beserta harga dasar.' : 'Manage standardized software development features and base pricing.')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label(app()->getLocale() === 'id' ? 'Nama Modul' : 'Module Name')
                            ->placeholder('e.g. Authentication & RBAC')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('category')
                            ->label(app()->getLocale() === 'id' ? 'Kategori / Domain' : 'Category / Domain')
                            ->placeholder('e.g. Core Security, Backend & API, Fintech')
                            ->maxLength(255),

                        Forms\Components\TextInput::make('base_price')
                            ->label(app()->getLocale() === 'id' ? 'Harga Dasar (IDR)' : 'Base Price (IDR)')
                            ->required()
                            ->currencyMask(thousandSeparator: '.', decimalSeparator: ',', precision: 2)
                            ->prefix('Rp')
                            ->minValue(0)
                            ->helperText(app()->getLocale() === 'id' ? 'Harga acuan standar sebelum dikalikan bobot kompleksitas.' : 'Standard base price before complexity multiplier and currency exchange.'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(app()->getLocale() === 'id' ? 'Nama Modul' : 'Module Name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('category')
                    ->label(app()->getLocale() === 'id' ? 'Kategori' : 'Category')
                    ->searchable()
                    ->badge()
                    ->color('info')
                    ->sortable(),

                Tables\Columns\TextColumn::make('base_price')
                    ->label(app()->getLocale() === 'id' ? 'Harga Dasar (IDR)' : 'Base Price (IDR)')
                    ->money('IDR', locale: 'id')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->label(app()->getLocale() === 'id' ? 'Filter Kategori' : 'Filter Category')
                    ->options(fn () => Module::query()->whereNotNull('category')->distinct()->pluck('category', 'category')->toArray()),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListModules::route('/'),
            'create' => Pages\CreateModule::route('/create'),
            'edit' => Pages\EditModule::route('/{record}/edit'),
        ];
    }
}
