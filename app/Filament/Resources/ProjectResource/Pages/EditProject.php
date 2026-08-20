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
        $total = 0;
        if (isset($data['items']) && is_array($data['items'])) {
            $exchangeRate = (float) ($data['exchange_rate'] ?? 1);
            if ($exchangeRate <= 0) {
                $exchangeRate = 1;
            }

            foreach ($data['items'] as $item) {
                $base = (float) ($item['base_price'] ?? 0);
                $weight = (float) ($item['complexity_weight'] ?? 1.0);
                $calculated = round(($base * $weight) / $exchangeRate, 2);
                $total += $calculated;
            }
        }
        $data['grand_total'] = $total;

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
