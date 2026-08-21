<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class QuickCalculatorWidget extends Widget
{
    protected static ?int $sort = 2;

    protected static string $view = 'filament.widgets.quick-calculator-widget';

    protected int | string | array $columnSpan = [
        'default' => 12,
        'md' => 5,
        'lg' => 4,
        'xl' => 4,
    ];

    public string $mode = 'one_off'; // one_off | subscription
    public string $basePrice = '2.000.000';
    public float $complexity = 1.00;
    public string $setupFee = '0';
    public string $subscriptionBasis = 'per_user'; // per_user | modular | hybrid
    public int $userCount = 50;
    public string $pricePerUser = '50.000';
    public int $duration = 12;

    public function setMode(string $mode): void
    {
        $this->mode = $mode;
    }

    public function updatedBasePrice($value): void
    {
        $clean = preg_replace('/[^\d]/', '', (string) $value);
        $this->basePrice = $clean !== '' ? number_format((float) $clean, 0, ',', '.') : '0';
    }

    public function updatedSetupFee($value): void
    {
        $clean = preg_replace('/[^\d]/', '', (string) $value);
        $this->setupFee = $clean !== '' ? number_format((float) $clean, 0, ',', '.') : '0';
    }

    public function updatedPricePerUser($value): void
    {
        $clean = preg_replace('/[^\d]/', '', (string) $value);
        $this->pricePerUser = $clean !== '' ? number_format((float) $clean, 0, ',', '.') : '0';
    }

    public function getCleanBasePriceProperty(): float
    {
        return (float) preg_replace('/[^\d]/', '', $this->basePrice);
    }

    public function getCleanSetupFeeProperty(): float
    {
        return (float) preg_replace('/[^\d]/', '', $this->setupFee);
    }

    public function getCleanPricePerUserProperty(): float
    {
        return (float) preg_replace('/[^\d]/', '', $this->pricePerUser);
    }

    public function getCalculatedTotalProperty(): float
    {
        return round($this->cleanBasePrice * $this->complexity, 2);
    }

    public function getOneOffTotalProperty(): float
    {
        return round($this->calculatedTotal + $this->cleanSetupFee, 2);
    }

    public function getMonthlyRecurringProperty(): float
    {
        $base = $this->calculatedTotal;
        $userCost = $this->userCount * $this->cleanPricePerUser;

        return match ($this->subscriptionBasis) {
            'per_user' => $userCost,
            'hybrid' => $base + $userCost,
            default => $base, // 'modular'
        };
    }

    public function getSubscriptionGrandTotalProperty(): float
    {
        return round($this->cleanSetupFee + ($this->monthlyRecurring * $this->duration), 2);
    }
}

