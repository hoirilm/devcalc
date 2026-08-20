<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use App\Filament\Resources\ProjectResource;
use Filament\Resources\Pages\CreateRecord;

class CreateProject extends CreateRecord
{
    protected static string $resource = ProjectResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();

        // Calculate grand total from items before create
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

    protected function afterCreate(): void
    {
        $this->record->recalculateGrandTotal();
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
