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

    protected static ?string $navigationGroup = 'Estimation';

    protected static ?int $navigationSort = 2;

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
                Forms\Components\Section::make('Project & Client Overview')
                    ->description('Specify project quotation metadata, target currency, and locked exchange rate.')
                    ->schema([
                        Forms\Components\TextInput::make('client_name')
                            ->label('Client Name / Organization')
                            ->placeholder('e.g. PT Maju Bersama Digital')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(2),

                        Forms\Components\Select::make('currency_code')
                            ->label('Currency')
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
                            ->label('Exchange Rate (in IDR)')
                            ->required()
                            ->numeric()
                            ->default(1.00)
                            ->minValue(0.01)
                            ->live(onBlur: true)
                            ->prefix('Rp')
                            ->helperText('1 unit of selected currency = X Rupiah.')
                            ->afterStateUpdated(function (Get $get, Set $set) {
                                static::recalculateAllItems($get, $set);
                            }),

                        Forms\Components\Select::make('status')
                            ->label('Quotation Status')
                            ->options([
                                'Draft' => 'Draft',
                                'Generated' => 'Generated',
                            ])
                            ->default('Draft')
                            ->required(),
                    ])->columns(3),

                Forms\Components\Section::make('Line Items / Features Scope')
                    ->description('Add standardized catalog modules or define custom custom development features.')
                    ->schema([
                        Forms\Components\Repeater::make('items')
                            ->relationship('items')
                            ->schema([
                                Forms\Components\Select::make('module_id')
                                    ->label('Feature Catalog Template')
                                    ->placeholder('-- Select Predefined Module (Optional) --')
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
                                    ->label('Feature / Task Name')
                                    ->placeholder('e.g. Multi-vendor Payment Engine')
                                    ->required()
                                    ->maxLength(255)
                                    ->columnSpan(2),

                                Forms\Components\TextInput::make('base_price')
                                    ->label('Base Price (IDR)')
                                    ->required()
                                    ->numeric()
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
                                    ->label('Complexity Weight')
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
                                    ->label('Calculated Price')
                                    ->required()
                                    ->numeric()
                                    ->disabled()
                                    ->dehydrated()
                                    ->prefix(fn (Get $get) => $get('../../currency_code') ?: 'IDR')
                                    ->helperText('Formula: (Base * Weight) / Exchange Rate')
                                    ->columnSpan(2),
                            ])
                            ->columns(4)
                            ->defaultItems(1)
                            ->reorderable()
                            ->collapsible()
                            ->cloneable()
                            ->itemLabel(fn (array $state): ?string => $state['item_name'] ?? 'New Item')
                            ->columnSpanFull(),

                        Forms\Components\Placeholder::make('grand_total_summary')
                            ->label('Estimated Total Quotation')
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
                    ->label('Client Name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Created By')
                    ->badge()
                    ->color('gray')
                    ->sortable(),

                Tables\Columns\TextColumn::make('currency_code')
                    ->label('Currency')
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
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'Draft' => 'Draft',
                        'Generated' => 'Generated',
                    ]),
                Tables\Filters\SelectFilter::make('currency_code')
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
