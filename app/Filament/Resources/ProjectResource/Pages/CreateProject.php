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

    protected function afterCreate(): void
    {
        $this->record->recalculateGrandTotal();
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
