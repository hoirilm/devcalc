<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use App\Filament\Resources\ProjectResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProject extends EditRecord
{
    protected static string $resource = ProjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('create_addendum')
                ->label('Buat Dokumen Adendum')
                ->icon('heroicon-o-document-duplicate')
                ->color('warning')
                ->visible(fn () => $this->record->billing_type === 'subscription' || $this->record->status === 'Generated')
                ->form([
                    \Filament\Forms\Components\Select::make('addendum_type')
                        ->label('Tipe Penyesuaian Adendum')
                        ->placeholder('-- Pilih Jenis Penyesuaian Adendum --')
                        ->options([
                            'user_capacity' => 'Penyesuaian / Penambahan Kapasitas User',
                            'module_expansion' => 'Penambahan Fitur / Modul Baru',
                            'contract_renewal' => 'Upgrade Penuh & Perpanjangan Kontrak',
                        ])
                        ->helperText('Pilih skenario perubahan ruang lingkup atau kapasitas kontrak.')
                        ->required(),

                    \Filament\Forms\Components\TextInput::make('remaining_duration')
                        ->label('Sisa Durasi Berjalan (Bulan / Tahun)')
                        ->placeholder('Contoh: 6')
                        ->helperText('Sisa periode kontrak berjalan yang akan ditagihkan (dalam bulan / tahun).')
                        ->numeric()
                        ->minValue(1)
                        ->required(),

                    \Filament\Forms\Components\TextInput::make('new_user_count')
                        ->label('Jumlah Kapasitas User (Total / Tambahan)')
                        ->placeholder('Contoh: 50 atau 100')
                        ->helperText('Masukkan kuota pengguna aktif baru atau penambahan kapasitas user.')
                        ->numeric()
                        ->minValue(1)
                        ->visible(fn () => $this->record->billing_type === 'subscription')
                        ->required(fn () => $this->record->billing_type === 'subscription'),

                    \Filament\Forms\Components\Textarea::make('addendum_notes')
                        ->label('Catatan Ruang Lingkup Adendum')
                        ->placeholder('Contoh: Penyesuaian kuota pengguna aktif dari 50 menjadi 100 user untuk sisa masa kontrak 6 bulan.')
                        ->helperText('Tuliskan rincian kesepakatan adendum yang akan dicantumkan pada surat penawaran resmi.')
                        ->required()
                        ->rows(3),
                ])
                ->action(function (array $data) {
                    $nextNum = $this->record->getNextAddendumNumber();
                    $newProject = \App\Models\Project::create([
                        'parent_id' => $this->record->id,
                        'quotation_type' => 'addendum',
                        'addendum_number' => $nextNum,
                        'user_id' => auth()->id(),
                        'client_name' => $this->record->client_name,
                        'status' => 'Draft',
                        'billing_type' => $this->record->billing_type,
                        'subscription_basis' => $this->record->subscription_basis,
                        'billing_cycle' => $this->record->billing_cycle,
                        'subscription_duration' => (int) ($data['remaining_duration'] ?? $this->record->subscription_duration),
                        'user_count' => (int) ($data['new_user_count'] ?? $this->record->user_count),
                        'price_per_user' => $this->record->price_per_user,
                        'setup_fee' => 0.00,
                        'addendum_notes' => $data['addendum_notes'] ?? null,
                        'grand_total' => 0.00,
                    ]);

                    if ($data['addendum_type'] !== 'user_capacity') {
                        foreach ($this->record->items as $item) {
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

            Actions\Action::make('print_pdf')
                ->label('Print / Download PDF')
                ->icon('heroicon-o-printer')
                ->color('success')
                ->url(fn (): string => route('projects.pdf', $this->record))
                ->openUrlInNewTab(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $itemsTotal = 0;
        if (isset($data['items']) && is_array($data['items'])) {
            foreach ($data['items'] as $item) {
                $base = (float) str_replace('.', '', (string) ($item['base_price'] ?? 0));
                $weight = (float) ($item['complexity_weight'] ?? 1.0);
                $calculated = round($base * $weight, 2);
                $itemsTotal += $calculated;
            }
        }

        $billingType = $data['billing_type'] ?? 'one_off';
        if ($billingType === 'subscription') {
            $basis = $data['subscription_basis'] ?? 'modular';
            $duration = (int) ($data['subscription_duration'] ?? 12);
            $setupFee = (float) str_replace('.', '', (string) ($data['setup_fee'] ?? 0));
            $userCount = (int) ($data['user_count'] ?? 0);
            $pricePerUser = (float) str_replace('.', '', (string) ($data['price_per_user'] ?? 0));
            $userRecurring = $userCount * $pricePerUser;

            $recurring = match ($basis) {
                'per_user' => $userRecurring,
                'hybrid' => $itemsTotal + $userRecurring,
                default => $itemsTotal,
            };

            $data['grand_total'] = $setupFee + ($recurring * $duration);
        } else {
            $data['grand_total'] = $itemsTotal;
        }

        return $data;
    }

    protected function afterSave(): void
    {
        $this->record->recalculateGrandTotal();
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
