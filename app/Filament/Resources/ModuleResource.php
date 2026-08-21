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
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('base_price')
                            ->label(app()->getLocale() === 'id' ? 'Harga Beli Putus (One-Off)' : 'One-Off Base Price')
                            ->required()
                            ->prefix('Rp')
                            ->mask(\Filament\Support\RawJs::make('$money($input, \',\', \'.\', 0)'))
                            ->stripCharacters('.')
                            ->formatStateUsing(fn ($state) => $state !== null ? (int) $state : 0)
                            ->helperText(app()->getLocale() === 'id' ? 'Harga dasar lisensi beli putus.' : 'Standard base price for one-off build.'),

                        Forms\Components\TextInput::make('subscription_price')
                            ->label(app()->getLocale() === 'id' ? 'Harga Langganan / Bulan' : 'Subscription Price / Month')
                            ->prefix('Rp')
                            ->mask(\Filament\Support\RawJs::make('$money($input, \',\', \'.\', 0)'))
                            ->stripCharacters('.')
                            ->formatStateUsing(fn ($state) => $state !== null ? (int) $state : 0)
                            ->default(0)
                            ->helperText(app()->getLocale() === 'id' ? 'Tarif sewa & pemeliharaan modul per bulan.' : 'Monthly maintenance & subscription fee.'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('name', 'asc')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(app()->getLocale() === 'id' ? 'Nama Modul & Deskripsi' : 'Module Name & Scope')
                    ->searchable(['name', 'description'])
                    ->sortable()
                    ->weight('bold')
                    ->icon('heroicon-o-cube')
                    ->iconColor('primary')
                    ->description(fn (Module $record): ?string => $record->description),

                Tables\Columns\TextColumn::make('category')
                    ->label(app()->getLocale() === 'id' ? 'Kategori Domain' : 'Category')
                    ->searchable()
                    ->badge()
                    ->color('info')
                    ->icon('heroicon-o-tag')
                    ->sortable(),

                Tables\Columns\TextColumn::make('base_price')
                    ->label(app()->getLocale() === 'id' ? 'Harga Beli Putus' : 'One-Off Price')
                    ->money('IDR', locale: 'id')
                    ->weight('bold')
                    ->sortable(),

                Tables\Columns\TextColumn::make('subscription_price')
                    ->label(app()->getLocale() === 'id' ? 'Langganan / Bulan' : 'Sub / Month')
                    ->money('IDR', locale: 'id')
                    ->weight('bold')
                    ->color('success')
                    ->description(fn (Module $record): string => $record->subscription_price > 0 ? 'Tarif pemeliharaan rutin' : 'Mengikuti estimasi 8%')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(app()->getLocale() === 'id' ? 'Dibuat' : 'Created')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->label(app()->getLocale() === 'id' ? 'Filter Kategori' : 'Filter Category')
                    ->options(fn () => Module::query()->whereNotNull('category')->distinct()->pluck('category', 'category')->toArray()),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make()
                        ->label('Ubah Modul')
                        ->icon('heroicon-o-pencil-square'),
                    Tables\Actions\DeleteAction::make()
                        ->label('Hapus'),
                ])
                ->icon('heroicon-m-ellipsis-vertical')
                ->tooltip('Menu Aksi'),
            ])
            ->emptyStateHeading('Katalog Modul Kosong')
            ->emptyStateDescription('Tambahkan fitur atau modul standar baru ke dalam katalog master data.')
            ->emptyStateIcon('heroicon-o-cube-transparent')
            ->emptyStateActions([
                Tables\Actions\Action::make('create')
                    ->label('Tambah Modul Baru')
                    ->url(ModuleResource::getUrl('create'))
                    ->icon('heroicon-o-plus')
                    ->button(),
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
