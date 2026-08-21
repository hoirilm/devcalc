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
    public float $complexity = 1.25;

    public function updatedBasePrice($value): void
    {
        $clean = preg_replace('/[^\d]/', '', (string) $value);
        if ($clean !== '') {
            $this->basePrice = number_format((float) $clean, 0, ',', '.');
        }
    }

    public function getCleanBasePriceProperty(): float
    {
        return (float) preg_replace('/[^\d]/', '', $this->basePrice);
    }

    public function getCalculatedTotalProperty(): float
    {
        return round($this->cleanBasePrice * $this->complexity, 2);
    }
}

