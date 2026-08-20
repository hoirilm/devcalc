<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class QuickCalculatorWidget extends Widget
{
    protected static ?int $sort = 2;

    protected static string $view = 'filament.widgets.quick-calculator-widget';

    protected int | string | array $columnSpan = [
        'md' => 1,
        'xl' => 1,
    ];

    public string $basePrice = '10.000.000';
    public string $currency = 'USD';
    public string $exchangeRate = '16.000';
    public float $complexity = 1.25;

    public function updatedCurrency($value): void
    {
        $rate = match ($value) {
            'USD' => 16000,
            'EUR' => 17500,
            'SGD' => 12000,
            default => 1,
        };
        $this->exchangeRate = number_format($rate, 0, ',', '.');
    }

    public function updatedBasePrice($value): void
    {
        $clean = preg_replace('/[^\d]/', '', (string) $value);
        if ($clean !== '') {
            $this->basePrice = number_format((float) $clean, 0, ',', '.');
        }
    }

    public function updatedExchangeRate($value): void
    {
        $clean = preg_replace('/[^\d]/', '', (string) $value);
        if ($clean !== '') {
            $this->exchangeRate = number_format((float) $clean, 0, ',', '.');
        }
    }

    public function getCleanBasePriceProperty(): float
    {
        return (float) preg_replace('/[^\d]/', '', $this->basePrice);
    }

    public function getCleanExchangeRateProperty(): float
    {
        return (float) (preg_replace('/[^\d]/', '', $this->exchangeRate) ?: 1);
    }

    public function getCalculatedTotalProperty(): float
    {
        $rate = $this->cleanExchangeRate;
        if ($rate <= 0) {
            return 0;
        }

        return round(($this->cleanBasePrice * $this->complexity) / $rate, 2);
    }
}
