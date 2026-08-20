<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProjectResource\Pages;
use App\Models\Module;
use App\Models\Project;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    protected static ?string $navigationIcon = 'heroicon-o-calculator';

    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): ?string
    {
        return app()->getLocale() === 'id' ? 'Kalkulator & Estimasi' : 'Estimation & Calculator';
    }

    public static function getNavigationLabel(): string
    {
        return app()->getLocale() === 'id' ? 'Daftar Penawaran' : 'Quotations';
    }

    public static function getModelLabel(): string
    {
        return app()->getLocale() === 'id' ? 'Penawaran Proyek' : 'Project Quotation';
    }

    public static function getPluralModelLabel(): string
    {
        return app()->getLocale() === 'id' ? 'Daftar Penawaran' : 'Quotations';
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();

        // If user is not Admin, only show their own quotations
        if ($user && ! $user->hasRole('Admin')) {
            $query->where('user_id', $user->id);
        }

        return $query;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(app()->getLocale() === 'id' ? 'Informasi Proyek & Klien' : 'Project & Client Overview')
                    ->description(app()->getLocale() === 'id' ? 'Tentukan data penawaran, target mata uang, dan nilai kurs tukar (lock-rate).' : 'Specify project quotation metadata, target currency, and locked exchange rate.')
                    ->schema([
                        Forms\Components\TextInput::make('client_name')
                            ->label(app()->getLocale() === 'id' ? 'Nama Klien / Perusahaan' : 'Client Name / Organization')
                            ->placeholder('e.g. PT Maju Bersama Digital')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(2),

                        Forms\Components\Select::make('currency_code')
                            ->label(app()->getLocale() === 'id' ? 'Mata Uang' : 'Currency')
                            ->options([
                                'IDR' => 'IDR - Indonesian Rupiah',
                                'USD' => 'USD - US Dollar',
                                'EUR' => 'EUR - Euro',
                                'SGD' => 'SGD - Singapore Dollar',
                            ])
                            ->default('IDR')
                            ->required()
                            ->live()
                            ->afterStateUpdated(function (Get $get, Set $set, ?string $state) {
                                // Provide sensible default exchange rate if switching currency
                                match ($state) {
                                    'USD' => $set('exchange_rate', 16000.00),
                                    'EUR' => $set('exchange_rate', 17500.00),
                                    'SGD' => $set('exchange_rate', 12000.00),
                                    default => $set('exchange_rate', 1.00),
                                };

                                // Recalculate all items based on new exchange rate
                                static::recalculateAllItems($get, $set);
                            }),

                        Forms\Components\TextInput::make('exchange_rate')
                            ->label(app()->getLocale() === 'id' ? 'Nilai Kurs (dalam IDR)' : 'Exchange Rate (in IDR)')
                            ->required()
                            ->currencyMask(thousandSeparator: '.', decimalSeparator: ',', precision: 2)
                            ->default(1.00)
                            ->minValue(0.01)
                            ->live(onBlur: true)
                            ->prefix('Rp')
                            ->helperText(app()->getLocale() === 'id' ? '1 unit mata uang terpilih = X Rupiah (Lock-Rate).' : '1 unit of selected currency = X Rupiah.')
                            ->afterStateUpdated(function (Get $get, Set $set) {
                                static::recalculateAllItems($get, $set);
                            }),

                        Forms\Components\Select::make('status')
                            ->label(app()->getLocale() === 'id' ? 'Status Penawaran' : 'Quotation Status')
                            ->options([
                                'Draft' => 'Draft',
                                'Generated' => 'Generated',
                            ])
                            ->default('Draft')
                            ->required(),
                    ])->columns(3),

                Forms\Components\Section::make(app()->getLocale() === 'id' ? 'Rincian Fitur & Lingkup Kerja' : 'Line Items / Features Scope')
                    ->description(app()->getLocale() === 'id' ? 'Tambahkan fitur standar dari katalog atau masukkan fitur pengembangan kustom.' : 'Add standardized catalog modules or define custom development features.')
                    ->schema([
                        Forms\Components\Repeater::make('items')
                            ->label(app()->getLocale() === 'id' ? 'Daftar Fitur' : 'Features List')
                            ->relationship('items')
                            ->schema([
                                Forms\Components\Select::make('module_id')
                                    ->label(app()->getLocale() === 'id' ? 'Pilih Template Katalog Modul' : 'Feature Catalog Template')
                                    ->placeholder(app()->getLocale() === 'id' ? '-- Pilih Modul Standar (Opsional) --' : '-- Select Predefined Module (Optional) --')
                                    ->options(Module::query()->pluck('name', 'id'))
                                    ->searchable()
                                    ->nullable()
                                    ->live()
                                    ->afterStateUpdated(function (Get $get, Set $set, ?string $state) {
                                        if ($state) {
                                            $module = Module::find($state);
                                            if ($module) {
                                                $set('item_name', $module->name);
                                                $set('base_price', $module->base_price);

                                                $exchangeRate = (float) ($get('../../exchange_rate') ?: 1);
                                                $complexity = (float) ($get('complexity_weight') ?: 1.00);
                                                $basePrice = (float) $module->base_price;

                                                $calc = $exchangeRate > 0
                                                    ? round(($basePrice * $complexity) / $exchangeRate, 2)
                                                    : 0;

                                                $set('calculated_price', $calc);
                                            }
                                        }
                                    })
                                    ->columnSpan(2),

                                Forms\Components\TextInput::make('item_name')
                                    ->label(app()->getLocale() === 'id' ? 'Nama Fitur / Tugas' : 'Feature / Task Name')
                                    ->placeholder('e.g. Multi-vendor Payment Engine')
                                    ->required()
                                    ->maxLength(255)
                                    ->columnSpan(2),

                                Forms\Components\TextInput::make('base_price')
                                    ->label(app()->getLocale() === 'id' ? 'Harga Dasar (IDR)' : 'Base Price (IDR)')
                                    ->required()
                                    ->currencyMask(thousandSeparator: '.', decimalSeparator: ',', precision: 2)
                                    ->prefix('Rp')
                                    ->minValue(0)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (Get $get, Set $set, ?string $state) {
                                        $basePrice = (float) ($state ?: 0);
                                        $complexity = (float) ($get('complexity_weight') ?: 1.00);
                                        $exchangeRate = (float) ($get('../../exchange_rate') ?: 1);

                                        $calc = $exchangeRate > 0
                                            ? round(($basePrice * $complexity) / $exchangeRate, 2)
                                            : 0;

                                        $set('calculated_price', $calc);
                                    })
                                    ->columnSpan(1),

                                Forms\Components\TextInput::make('complexity_weight')
                                    ->label(app()->getLocale() === 'id' ? 'Bobot Kompleksitas' : 'Complexity Weight')
                                    ->required()
                                    ->numeric()
                                    ->default(1.00)
                                    ->minValue(0.1)
                                    ->step(0.1)
                                    ->suffix('x')
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (Get $get, Set $set, ?string $state) {
                                        $complexity = (float) ($state ?: 1.00);
                                        $basePrice = (float) ($get('base_price') ?: 0);
                                        $exchangeRate = (float) ($get('../../exchange_rate') ?: 1);

                                        $calc = $exchangeRate > 0
                                            ? round(($basePrice * $complexity) / $exchangeRate, 2)
                                            : 0;

                                        $set('calculated_price', $calc);
                                    })
                                    ->columnSpan(1),

                                Forms\Components\TextInput::make('calculated_price')
                                    ->label(app()->getLocale() === 'id' ? 'Harga Terhitung' : 'Calculated Price')
                                    ->required()
                                    ->currencyMask(thousandSeparator: '.', decimalSeparator: ',', precision: 2)
                                    ->disabled()
                                    ->dehydrated()
                                    ->prefix(fn (Get $get) => $get('../../currency_code') ?: 'IDR')
                                    ->helperText(app()->getLocale() === 'id' ? 'Rumus: (Harga Dasar * Bobot) / Kurs' : 'Formula: (Base * Weight) / Exchange Rate')
                                    ->columnSpan(2),
                            ])
                            ->columns(4)
                            ->defaultItems(1)
                            ->reorderable()
                            ->collapsible()
                            ->cloneable()
                            ->itemLabel(fn (array $state): ?string => $state['item_name'] ?? (app()->getLocale() === 'id' ? 'Item Baru' : 'New Item'))
                            ->columnSpanFull(),

                        Forms\Components\Placeholder::make('grand_total_summary')
                            ->label(app()->getLocale() === 'id' ? 'Estimasi Total Penawaran' : 'Estimated Total Quotation')
                            ->content(function (Get $get) {
                                $items = $get('items') ?? [];
                                $currency = $get('currency_code') ?? 'IDR';
                                $total = 0;

                                foreach ($items as $item) {
                                    $total += (float) ($item['calculated_price'] ?? 0);
                                }

                                return \Illuminate\Support\Number::currency(
                                    $total,
                                    $currency,
                                    $currency === 'IDR' ? 'id' : 'en'
                                );
                            }),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('#ID')
                    ->formatStateUsing(fn ($state) => 'QUO-' . str_pad($state, 5, '0', STR_PAD_LEFT))
                    ->sortable(),

                Tables\Columns\TextColumn::make('client_name')
                    ->label(app()->getLocale() === 'id' ? 'Nama Klien' : 'Client Name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('user.name')
                    ->label(app()->getLocale() === 'id' ? 'Dibuat Oleh' : 'Created By')
                    ->badge()
                    ->color('gray')
                    ->sortable(),

                Tables\Columns\TextColumn::make('currency_code')
                    ->label(app()->getLocale() === 'id' ? 'Mata Uang' : 'Currency')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'USD' => 'success',
                        'EUR' => 'warning',
                        'SGD' => 'info',
                        default => 'primary',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('grand_total')
                    ->label('Grand Total')
                    ->formatStateUsing(fn ($record) => \Illuminate\Support\Number::currency(
                        $record->grand_total,
                        $record->currency_code,
                        $record->currency_code === 'IDR' ? 'id' : 'en'
                    ))
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Generated' => 'success',
                        default => 'warning',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(app()->getLocale() === 'id' ? 'Tanggal Dibuat' : 'Created Date')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label(app()->getLocale() === 'id' ? 'Filter Status' : 'Filter Status')
                    ->options([
                        'Draft' => 'Draft',
                        'Generated' => 'Generated',
                    ]),
                Tables\Filters\SelectFilter::make('currency_code')
                    ->label(app()->getLocale() === 'id' ? 'Filter Mata Uang' : 'Filter Currency')
                    ->options([
                        'IDR' => 'IDR',
                        'USD' => 'USD',
                        'EUR' => 'EUR',
                        'SGD' => 'SGD',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('print_pdf')
                    ->label('PDF')
                    ->icon('heroicon-o-printer')
                    ->color('success')
                    ->url(fn (Project $record): string => route('projects.pdf', $record))
                    ->openUrlInNewTab(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    protected static function recalculateAllItems(Get $get, Set $set): void
    {
        $items = $get('items') ?? [];
        $exchangeRate = (float) ($get('exchange_rate') ?: 1);

        if ($exchangeRate <= 0) {
            $exchangeRate = 1;
        }

        foreach ($items as $key => $item) {
            $basePrice = (float) ($item['base_price'] ?? 0);
            $complexity = (float) ($item['complexity_weight'] ?? 1.00);

            $calc = round(($basePrice * $complexity) / $exchangeRate, 2);
            $set("items.{$key}.calculated_price", $calc);
        }
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
            'index' => Pages\ListProjects::route('/'),
            'create' => Pages\CreateProject::route('/create'),
            'edit' => Pages\EditProject::route('/{record}/edit'),
        ];
    }
}
