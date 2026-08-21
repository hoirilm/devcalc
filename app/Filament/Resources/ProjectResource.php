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
                Forms\Components\Section::make(app()->getLocale() === 'id' ? 'Informasi Dokumen & Adendum' : 'Document & Addendum Information')
                    ->visible(fn (?Project $record) => $record && $record->isAddendum())
                    ->schema([
                        Forms\Components\Placeholder::make('parent_info')
                            ->label('Status Adendum')
                            ->content(function (?Project $record) {
                                if (!$record || !$record->isAddendum()) return null;
                                $parentCode = $record->parent ? $record->parent->getQuotationCode() : "QUO-" . str_pad($record->parent_id, 5, '0', STR_PAD_LEFT);
                                return new \Illuminate\Support\HtmlString(
                                    "<div style='padding: 10px 14px; background: #fef3c7; border: 1px solid #fde68a; border-radius: 8px; color: #92400e; font-size: 13px;'>" .
                                    "<strong>Dokumen Adendum Resmi:</strong> Penawaran ini merupakan adendum nomor <strong>#{$record->getQuotationCode()}</strong> yang mengacu pada Kontrak Induk <strong>#{$parentCode}</strong>." .
                                    "</div>"
                                );
                            })
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('addendum_notes')
                            ->label('Catatan / Ruang Lingkup Perubahan Adendum')
                            ->placeholder('Jelaskan ruang lingkup perubahan atau penyesuaian kapasitas pada adendum ini.')
                            ->rows(2)
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make(app()->getLocale() === 'id' ? 'Informasi Proyek & Klien' : 'Project & Client Overview')
                    ->description(app()->getLocale() === 'id' ? 'Tentukan data penawaran, klien, dan skema kontrak.' : 'Specify project quotation metadata, client, and billing scheme.')
                    ->schema([
                        Forms\Components\TextInput::make('client_name')
                            ->label(app()->getLocale() === 'id' ? 'Nama Klien / Perusahaan' : 'Client Name / Organization')
                            ->placeholder('e.g. PT Maju Bersama Digital')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(2),

                        Forms\Components\Select::make('status')
                            ->label(app()->getLocale() === 'id' ? 'Status Penawaran' : 'Quotation Status')
                            ->options([
                                'Draft' => 'Draft',
                                'Generated' => 'Generated',
                            ])
                            ->default('Draft')
                            ->required()
                            ->columnSpan(1),

                        Forms\Components\Select::make('billing_type')
                            ->label(app()->getLocale() === 'id' ? 'Skema Kontrak' : 'Contract Scheme')
                            ->options([
                                'one_off' => app()->getLocale() === 'id' ? 'Putus Kontrak (One-Off)' : 'One-Off (Fixed Contract)',
                                'subscription' => app()->getLocale() === 'id' ? 'Berlangganan / SaaS (Subscription)' : 'Subscription (SaaS/Retainer)',
                            ])
                            ->default('one_off')
                            ->required()
                            ->live()
                            ->columnSpan(1),

                        Forms\Components\Select::make('subscription_basis')
                            ->label(app()->getLocale() === 'id' ? 'Metode Tagihan Langganan' : 'Subscription Pricing Basis')
                            ->options([
                                'modular' => app()->getLocale() === 'id' ? 'Flat Modular (Sewa Modul)' : 'Modular (Per-Module)',
                                'per_user' => app()->getLocale() === 'id' ? 'Per-User (Kapasitas Pengguna)' : 'User-Based (Per-Seat)',
                                'hybrid' => app()->getLocale() === 'id' ? 'Hybrid (Modul + Kuota Pengguna)' : 'Hybrid (Module + Per-User)',
                            ])
                            ->default('modular')
                            ->required(fn (Get $get) => $get('billing_type') === 'subscription')
                            ->visible(fn (Get $get) => $get('billing_type') === 'subscription')
                            ->live()
                            ->columnSpan(1),

                        Forms\Components\Select::make('billing_cycle')
                            ->label(app()->getLocale() === 'id' ? 'Siklus Penagihan' : 'Billing Cycle')
                            ->options([
                                'monthly' => app()->getLocale() === 'id' ? 'Bulanan (Monthly)' : 'Monthly',
                                'yearly' => app()->getLocale() === 'id' ? 'Tahunan (Yearly)' : 'Yearly',
                            ])
                            ->default('monthly')
                            ->required(fn (Get $get) => $get('billing_type') === 'subscription')
                            ->visible(fn (Get $get) => $get('billing_type') === 'subscription')
                            ->live()
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('subscription_duration')
                            ->label(app()->getLocale() === 'id' ? 'Durasi Komitmen Kontrak' : 'Commitment Duration')
                            ->required(fn (Get $get) => $get('billing_type') === 'subscription')
                            ->visible(fn (Get $get) => $get('billing_type') === 'subscription')
                            ->numeric()
                            ->default(12)
                            ->minValue(1)
                            ->suffix(fn (Get $get) => $get('billing_cycle') === 'yearly' ? (app()->getLocale() === 'id' ? 'Tahun' : 'Years') : (app()->getLocale() === 'id' ? 'Bulan' : 'Months'))
                            ->live(onBlur: true)
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('user_count')
                            ->label(app()->getLocale() === 'id' ? 'Jumlah Pengguna Aktif (User)' : 'Estimated Active Users')
                            ->visible(fn (Get $get) => $get('billing_type') === 'subscription' && in_array($get('subscription_basis'), ['per_user', 'hybrid']))
                            ->numeric()
                            ->default(50)
                            ->minValue(1)
                            ->suffix(app()->getLocale() === 'id' ? 'User' : 'Seats')
                            ->live(onBlur: true)
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('price_per_user')
                            ->label(app()->getLocale() === 'id' ? 'Tarif per User / Bulan' : 'Price per User / Month')
                            ->visible(fn (Get $get) => $get('billing_type') === 'subscription' && in_array($get('subscription_basis'), ['per_user', 'hybrid']))
                            ->prefix('Rp')
                            ->mask(\Filament\Support\RawJs::make('$money($input, \',\', \'.\', 0)'))
                            ->stripCharacters('.')
                            ->formatStateUsing(fn ($state) => $state !== null ? (int) $state : 50000)
                            ->default(50000)
                            ->live(onBlur: true)
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('setup_fee')
                            ->label(app()->getLocale() === 'id' ? 'Biaya Setup / Onboarding Awal' : 'Initial Setup / Onboarding Fee')
                            ->visible(fn (Get $get) => $get('billing_type') === 'subscription')
                            ->prefix('Rp')
                            ->mask(\Filament\Support\RawJs::make('$money($input, \',\', \'.\', 0)'))
                            ->stripCharacters('.')
                            ->formatStateUsing(fn ($state) => $state !== null ? (int) $state : 0)
                            ->default(0)
                            ->helperText(app()->getLocale() === 'id' ? 'Biaya satu kali saat implementasi awal (opsional).' : 'One-time initial fee (optional).')
                            ->live(onBlur: true)
                            ->columnSpan(2),

                        Forms\Components\Select::make('maintenance_months')
                            ->label(app()->getLocale() === 'id' ? 'Masa Garansi Maintenance (SLA)' : 'Maintenance SLA Guarantee')
                            ->options([
                                1 => app()->getLocale() === 'id' ? '1 Bulan' : '1 Month',
                                3 => app()->getLocale() === 'id' ? '3 Bulan (Standar SLA)' : '3 Months (Standard SLA)',
                                6 => app()->getLocale() === 'id' ? '6 Bulan (Extended SLA)' : '6 Months (Extended SLA)',
                                12 => app()->getLocale() === 'id' ? '12 Bulan (Full Year SLA)' : '12 Months (Full Year SLA)',
                            ])
                            ->default(3)
                            ->required()
                            ->helperText(app()->getLocale() === 'id' ? 'Dukungan perbaikan bug gratis pasca serah terima.' : 'Free post-handover bugfix support.')
                            ->columnSpan(1),
                    ])->columns(3),

                Forms\Components\Section::make(app()->getLocale() === 'id' ? 'Rincian Fitur & Lingkup Kerja' : 'Line Items / Features Scope')
                    ->description(fn (Get $get) => $get('billing_type') === 'subscription' && $get('subscription_basis') === 'per_user'
                        ? (app()->getLocale() === 'id' 
                            ? '💡 Pada skema Per-User, modul yang dipilih berfungsi sebagai rincian cakupan fitur yang didapatkan klien (Termasuk dalam tarif lisensi user / All-Inclusive).' 
                            : '💡 Under Per-User scheme, selected modules serve as the included feature scope for the client (All-Inclusive in user pricing).')
                        : (app()->getLocale() === 'id' 
                            ? 'Tambahkan fitur standar dari katalog atau masukkan fitur pengembangan kustom.' 
                            : 'Add standardized catalog modules or define custom development features.'))
                    ->schema([
                        Forms\Components\Repeater::make('items')
                            ->label(app()->getLocale() === 'id' ? 'Daftar Fitur' : 'Features List')
                            ->relationship('items')
                            ->extraAttributes(['class' => 'devcalc-repeater-features'])
                            ->addActionLabel(app()->getLocale() === 'id' ? 'Tambahkan Fitur' : 'Add Feature Item')
                            ->deleteAction(
                                fn (\Filament\Forms\Components\Actions\Action $action) => $action
                                    ->requiresConfirmation(false)
                            )
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

                                                $billingType = $get('../../billing_type') ?? 'one_off';
                                                if ($billingType === 'subscription') {
                                                    $basePrice = (float) ($module->subscription_price > 0 ? $module->subscription_price : round($module->base_price * 0.08, 2));
                                                } else {
                                                    $basePrice = (float) $module->base_price;
                                                }

                                                $set('base_price', (int) $basePrice);

                                                $complexity = (float) ($get('complexity_weight') ?: 1.00);
                                                $calc = round($basePrice * $complexity, 2);
                                                $set('calculated_price', (int) $calc);
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
                                    ->label(app()->getLocale() === 'id' ? 'Harga Dasar' : 'Base Price')
                                    ->required()
                                    ->prefix('Rp')
                                    ->mask(\Filament\Support\RawJs::make('$money($input, \',\', \'.\', 0)'))
                                    ->stripCharacters('.')
                                    ->formatStateUsing(fn ($state) => $state !== null ? (int) $state : 0)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (Get $get, Set $set, ?string $state) {
                                        $cleanBase = (float) str_replace('.', '', (string) ($state ?: 0));
                                        $complexity = (float) ($get('complexity_weight') ?: 1.00);

                                        $calc = round($cleanBase * $complexity, 2);
                                        $set('calculated_price', (int) $calc);
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
                                        $rawBase = $get('base_price') ?: 0;
                                        $basePrice = (float) str_replace('.', '', (string) $rawBase);

                                        $calc = round($basePrice * $complexity, 2);
                                        $set('calculated_price', (int) $calc);
                                    })
                                    ->columnSpan(1),

                                Forms\Components\TextInput::make('calculated_price')
                                    ->label(app()->getLocale() === 'id' ? 'Harga Terhitung' : 'Calculated Price')
                                    ->required()
                                    ->prefix('Rp')
                                    ->mask(\Filament\Support\RawJs::make('$money($input, \',\', \'.\', 0)'))
                                    ->stripCharacters('.')
                                    ->formatStateUsing(fn ($state) => $state !== null ? (int) $state : 0)
                                    ->disabled()
                                    ->dehydrated()
                                    ->helperText(app()->getLocale() === 'id' ? 'Rumus: Harga Dasar × Bobot Kompleksitas' : 'Formula: Base Price × Complexity Weight')
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
                                $itemsTotal = 0;

                                foreach ($items as $item) {
                                    $rawPrice = $item['calculated_price'] ?? 0;
                                    $itemsTotal += (float) str_replace('.', '', (string) $rawPrice);
                                }

                                $billingType = $get('billing_type') ?? 'one_off';

                                if ($billingType === 'subscription') {
                                    $basis = $get('subscription_basis') ?? 'modular';
                                    $cycle = $get('billing_cycle') ?? 'monthly';
                                    $duration = (int) ($get('subscription_duration') ?: 1);
                                    $setupFee = (float) str_replace('.', '', (string) ($get('setup_fee') ?: 0));

                                    $userCount = (int) ($get('user_count') ?: 0);
                                    $pricePerUser = (float) str_replace('.', '', (string) ($get('price_per_user') ?: 0));
                                    $userRecurring = $userCount * $pricePerUser;

                                    $recurringTotal = match ($basis) {
                                        'per_user' => $userRecurring,
                                        'hybrid' => $itemsTotal + $userRecurring,
                                        default => $itemsTotal,
                                    };

                                    $grandTotal = $setupFee + ($recurringTotal * $duration);

                                    $cycleText = $cycle === 'yearly' ? (app()->getLocale() === 'id' ? '/ tahun' : '/ year') : (app()->getLocale() === 'id' ? '/ bulan' : '/ month');
                                    $unitText = $cycle === 'yearly' ? (app()->getLocale() === 'id' ? 'Tahun' : 'Years') : (app()->getLocale() === 'id' ? 'Bulan' : 'Months');

                                    $recurringFormatted = \Illuminate\Support\Number::currency($recurringTotal, 'IDR', 'id') . ' ' . $cycleText;
                                    $grandFormatted = \Illuminate\Support\Number::currency($grandTotal, 'IDR', 'id');
                                    $setupFormatted = \Illuminate\Support\Number::currency($setupFee, 'IDR', 'id');

                                    $breakdown = [];
                                    if ($basis === 'hybrid' || $basis === 'modular') {
                                        $breakdown[] = "Biaya Modul: " . \Illuminate\Support\Number::currency($itemsTotal, 'IDR', 'id') . $cycleText;
                                    }
                                    if ($basis === 'hybrid' || $basis === 'per_user') {
                                        $breakdown[] = "Pengguna: {$userCount} User @ " . \Illuminate\Support\Number::currency($pricePerUser, 'IDR', 'id') . " (" . \Illuminate\Support\Number::currency($userRecurring, 'IDR', 'id') . "{$cycleText})";
                                    }
                                    if ($setupFee > 0) {
                                        $breakdown[] = "Setup Fee: {$setupFormatted}";
                                    }
                                    $breakdown[] = "Komitmen: {$duration} {$unitText}";

                                    $breakdownHtml = "<div style='font-size: 11.5px; color: #64748b; margin-top: 2px;'>" . implode(' | ', $breakdown) . "</div>";

                                    return new \Illuminate\Support\HtmlString(
                                        "<div style='line-height: 1.6;'>" .
                                        "<div style='font-size: 14px; font-weight: 600; color: #2563eb;'>Biaya Berulang: <strong>{$recurringFormatted}</strong></div>" .
                                        $breakdownHtml .
                                        "<div style='font-size: 16px; font-weight: 700; color: #0f172a; margin-top: 4px;'>Total Nilai Kontrak: {$grandFormatted}</div>" .
                                        "</div>"
                                    );
                                }

                                return \Illuminate\Support\Number::currency(
                                    $itemsTotal,
                                    'IDR',
                                    'id'
                                );
                            }),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('client_name')
                    ->label(app()->getLocale() === 'id' ? 'Penawaran & Klien' : 'Quotation & Client')
                    ->searchable(['client_name', 'id'])
                    ->sortable()
                    ->weight('bold')
                    ->description(function (Project $record): string {
                        $code = $record->getQuotationCode();
                        $date = $record->created_at ? $record->created_at->format('d M Y') : '-';
                        return "#{$code} • {$date}";
                    })
                    ->icon('heroicon-o-briefcase')
                    ->iconColor('primary'),

                Tables\Columns\TextColumn::make('billing_type')
                    ->label(app()->getLocale() === 'id' ? 'Skema & Tipe' : 'Scheme & Type')
                    ->badge()
                    ->formatStateUsing(function (Project $record): string {
                        if ($record->billing_type === 'subscription') {
                            $basis = match ($record->subscription_basis) {
                                'per_user' => 'Per-User',
                                'hybrid' => 'Hybrid',
                                default => 'Modular',
                            };
                            return "Langganan ({$basis})";
                        }
                        return 'Putus Kontrak';
                    })
                    ->color(fn (Project $record): string => $record->billing_type === 'subscription' ? 'info' : 'gray')
                    ->description(function (Project $record): ?string {
                        if ($record->isAddendum()) {
                            $parentCode = $record->parent ? $record->parent->getQuotationCode() : "ID #{$record->parent_id}";
                            return "📑 Adendum (Induk: #{$parentCode})";
                        }
                        if ($record->billing_type === 'subscription') {
                            $cycle = $record->billing_cycle === 'yearly' ? 'Tahunan' : 'Bulanan';
                            if (in_array($record->subscription_basis, ['per_user', 'hybrid'])) {
                                return "{$record->user_count} User • {$record->subscription_duration} {$cycle}";
                            }
                            return "{$record->subscription_duration} {$cycle}";
                        }
                        return $record->items->count() . ' Modul Terhitung';
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('grand_total')
                    ->label(app()->getLocale() === 'id' ? 'Nilai Penawaran' : 'Quotation Value')
                    ->formatStateUsing(fn ($record) => \Illuminate\Support\Number::currency(
                        $record->grand_total,
                        'IDR',
                        'id'
                    ))
                    ->description(function (Project $record): ?string {
                        if ($record->billing_type === 'subscription') {
                            $recurring = \Illuminate\Support\Number::currency($record->getRecurringPerCycle(), 'IDR', 'id');
                            $cycle = $record->billing_cycle === 'yearly' ? '/th' : '/bln';
                            return "Berulang: {$recurring} {$cycle}";
                        }
                        return null;
                    })
                    ->sortable()
                    ->weight('bold')
                    ->color('primary'),

                Tables\Columns\TextColumn::make('user.name')
                    ->label(app()->getLocale() === 'id' ? 'Estimator' : 'Estimator')
                    ->badge()
                    ->color('gray')
                    ->icon('heroicon-o-user')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Generated' => 'success',
                        default => 'warning',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'Generated' => 'Resmi (Generated)',
                        default => 'Draft Berjalan',
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'Generated' => 'heroicon-s-check-circle',
                        default => 'heroicon-s-clock',
                    })
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('quotation_type')
                    ->label(app()->getLocale() === 'id' ? 'Tipe Dokumen' : 'Document Type')
                    ->options([
                        'standard' => 'Kontrak Standar',
                        'addendum' => 'Adendum Penyesuaian',
                    ]),

                Tables\Filters\SelectFilter::make('billing_type')
                    ->label(app()->getLocale() === 'id' ? 'Skema Kontrak' : 'Billing Scheme')
                    ->options([
                        'one_off' => app()->getLocale() === 'id' ? 'Putus Kontrak' : 'One-Off',
                        'subscription' => app()->getLocale() === 'id' ? 'Berlangganan (Subscription)' : 'Subscription',
                    ]),

                Tables\Filters\SelectFilter::make('subscription_basis')
                    ->label(app()->getLocale() === 'id' ? 'Metode Langganan' : 'Subscription Basis')
                    ->options([
                        'modular' => 'Flat Modular',
                        'per_user' => 'Per-User',
                        'hybrid' => 'Hybrid',
                    ]),

                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'Draft' => 'Draft',
                        'Generated' => 'Generated',
                    ]),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('view_summary')
                        ->label('Lihat Ringkasan')
                        ->icon('heroicon-o-eye')
                        ->color('info')
                        ->modalHeading(fn (Project $record) => "Ringkasan Penawaran #{$record->getQuotationCode()}")
                        ->modalDescription(fn (Project $record) => "Klien: {$record->client_name} • Dibuat oleh {$record->user->name}")
                        ->modalSubmitAction(false)
                        ->modalCancelActionLabel('Tutup')
                        ->modalContent(fn (Project $record) => view('filament.modals.project-summary', ['project' => $record])),

                    Tables\Actions\Action::make('print_pdf')
                        ->label('Cetak / Unduh PDF')
                        ->icon('heroicon-o-printer')
                        ->color('success')
                        ->url(fn (Project $record): string => route('projects.pdf', $record))
                        ->openUrlInNewTab(),

                    Tables\Actions\Action::make('create_addendum')
                        ->label('Buat Adendum')
                        ->icon('heroicon-o-document-duplicate')
                        ->color('warning')
                        ->visible(fn (Project $record) => $record->billing_type === 'subscription' || $record->status === 'Generated')
                        ->form([
                            Forms\Components\Select::make('addendum_type')
                                ->label('Tipe Penyesuaian Adendum')
                                ->placeholder('-- Pilih Jenis Penyesuaian Adendum --')
                                ->options([
                                    'user_capacity' => 'Penyesuaian / Penambahan Kapasitas User',
                                    'module_expansion' => 'Penambahan Fitur / Modul Baru',
                                    'contract_renewal' => 'Upgrade Penuh & Perpanjangan Kontrak',
                                ])
                                ->helperText('Pilih skenario perubahan ruang lingkup atau kapasitas kontrak.')
                                ->required()
                                ->live(),

                            Forms\Components\TextInput::make('remaining_duration')
                                ->label('Sisa Durasi Berjalan (Bulan / Tahun)')
                                ->placeholder('Contoh: 6')
                                ->helperText('Sisa periode kontrak berjalan yang akan ditagihkan (dalam bulan / tahun).')
                                ->numeric()
                                ->minValue(1)
                                ->required(),

                            Forms\Components\TextInput::make('new_user_count')
                                ->label('Jumlah Kapasitas User (Total / Tambahan)')
                                ->placeholder('Contoh: 50 atau 100')
                                ->helperText('Masukkan kuota pengguna aktif baru atau penambahan kapasitas user.')
                                ->numeric()
                                ->minValue(1)
                                ->visible(fn (Project $record) => $record->billing_type === 'subscription')
                                ->required(fn (Project $record) => $record->billing_type === 'subscription'),

                            Forms\Components\Textarea::make('addendum_notes')
                                ->label('Catatan Ruang Lingkup Adendum')
                                ->placeholder('Contoh: Penyesuaian kuota pengguna aktif dari 50 menjadi 100 user untuk sisa masa kontrak 6 bulan.')
                                ->helperText('Tuliskan rincian kesepakatan adendum yang akan dicantumkan pada surat penawaran resmi.')
                                ->required()
                                ->rows(3),
                        ])
                        ->action(function (Project $record, array $data) {
                            $nextNum = $record->getNextAddendumNumber();
                            $newProject = Project::create([
                                'parent_id' => $record->id,
                                'quotation_type' => 'addendum',
                                'addendum_number' => $nextNum,
                                'user_id' => auth()->id(),
                                'client_name' => $record->client_name,
                                'status' => 'Draft',
                                'billing_type' => $record->billing_type,
                                'subscription_basis' => $record->subscription_basis,
                                'billing_cycle' => $record->billing_cycle,
                                'subscription_duration' => (int) ($data['remaining_duration'] ?? $record->subscription_duration),
                                'user_count' => (int) ($data['new_user_count'] ?? $record->user_count),
                                'price_per_user' => $record->price_per_user,
                                'setup_fee' => 0.00,
                                'addendum_notes' => $data['addendum_notes'] ?? null,
                                'grand_total' => 0.00,
                            ]);

                            if ($data['addendum_type'] !== 'user_capacity') {
                                foreach ($record->items as $item) {
                                    $newProject->items()->create([
                                        'module_id' => $item->module_id,
                                        'item_name' => $item->item_name,
                                        'base_price' => $item->base_price,
                                        'complexity_weight' => $item->complexity_weight,
                                        'calculated_price' => $item->calculated_price,
                                    ]);
                                }
                            }

                            $newProject->recalculateGrandTotal();

                            \Filament\Notifications\Notification::make()
                                ->title("Dokumen Adendum #{$newProject->getQuotationCode()} Berhasil Dibuat")
                                ->success()
                                ->send();

                            return redirect(ProjectResource::getUrl('edit', ['record' => $newProject]));
                        }),

                    Tables\Actions\EditAction::make()
                        ->label('Ubah Penawaran')
                        ->icon('heroicon-o-pencil-square'),

                    Tables\Actions\DeleteAction::make()
                        ->label('Hapus'),
                ])
                ->icon('heroicon-m-ellipsis-vertical')
                ->tooltip('Menu Aksi'),
            ])
            ->emptyStateHeading('Belum Ada Penawaran')
            ->emptyStateDescription('Mulai buat kalkulasi estimasi biaya atau penawaran harga software baru.')
            ->emptyStateIcon('heroicon-o-document-chart-bar')
            ->emptyStateActions([
                Tables\Actions\Action::make('create')
                    ->label('Buat Penawaran Baru')
                    ->url(ProjectResource::getUrl('create'))
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
            'index' => Pages\ListProjects::route('/'),
            'create' => Pages\CreateProject::route('/create'),
            'edit' => Pages\EditProject::route('/{record}/edit'),
        ];
    }
}
