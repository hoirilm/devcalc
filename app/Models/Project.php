<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'parent_id',
        'quotation_type',
        'addendum_number',
        'user_id',
        'client_id',
        'deal_id',
        'client_name',
        'project_category',
        'estimated_timeline',
        'grand_total',
        'status',
        'billing_type',
        'subscription_basis',
        'billing_cycle',
        'apply_annual_discount',
        'discount_percentage',
        'subscription_duration',
        'user_count',
        'price_per_user',
        'setup_fee',
        'maintenance_months',
        'notes',
        'addendum_notes',
    ];

    protected function casts(): array
    {
        return [
            'grand_total' => 'decimal:2',
            'setup_fee' => 'decimal:2',
            'price_per_user' => 'decimal:2',
            'apply_annual_discount' => 'boolean',
            'discount_percentage' => 'decimal:2',
            'subscription_duration' => 'integer',
            'user_count' => 'integer',
            'maintenance_months' => 'integer',
            'addendum_number' => 'integer',
        ];
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'parent_id');
    }

    public function addendums(): HasMany
    {
        return $this->hasMany(Project::class, 'parent_id')->orderBy('addendum_number');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function deal(): BelongsTo
    {
        return $this->belongsTo(Deal::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(ProjectItem::class, 'project_id');
    }

    public function isSubscription(): bool
    {
        return $this->billing_type === 'subscription';
    }

    public function getMaintenanceMonths(): int
    {
        return (int) ($this->maintenance_months ?: 3);
    }

    public function isAddendum(): bool
    {
        return $this->quotation_type === 'addendum' || !empty($this->parent_id);
    }

    public function getQuotationCode(): string
    {
        if ($this->isAddendum() && $this->parent_id) {
            $parentNum = str_pad($this->parent_id, 5, '0', STR_PAD_LEFT);
            $addNum = str_pad($this->addendum_number ?: 1, 2, '0', STR_PAD_LEFT);
            return "QUO-{$parentNum}-ADD-{$addNum}";
        }

        return 'QUO-' . str_pad($this->id ?: 0, 5, '0', STR_PAD_LEFT);
    }

    public function getNextAddendumNumber(): int
    {
        $max = (int) $this->addendums()->max('addendum_number');
        return $max + 1;
    }

    public function getHostingTotal(): float
    {
        return (float) $this->items()
            ->whereNull('module_id')
            ->where(function ($q) {
                $q->where('item_name', 'like', '%Hosting%')
                  ->orWhere('item_name', 'like', '%Server%')
                  ->orWhere('item_name', 'like', '%VPS%')
                  ->orWhere('item_name', 'like', '%cPanel%');
            })
            ->sum('calculated_price');
    }

    public function getSoftwareItemsTotal(): float
    {
        return (float) $this->items()
            ->where(function ($q) {
                $q->whereNotNull('module_id')
                  ->orWhere(function ($q2) {
                      $q2->where('item_name', 'not like', '%Hosting%')
                         ->where('item_name', 'not like', '%Server%')
                         ->where('item_name', 'not like', '%VPS%')
                         ->where('item_name', 'not like', '%cPanel%');
                  });
            })
            ->sum('calculated_price');
    }

    public function getBaseMonthlyRecurring(): float
    {
        $hostingTotal = $this->getHostingTotal();
        $softwareTotal = $this->getSoftwareItemsTotal();
        $userTotal = ((int) ($this->user_count ?: 0)) * ((float) ($this->price_per_user ?: 0));

        return match ($this->subscription_basis) {
            'per_user' => $userTotal + $hostingTotal,
            'hybrid' => $softwareTotal + $hostingTotal + $userTotal,
            default => $softwareTotal + $hostingTotal, // 'modular'
        };
    }

    public function getRecurringPerCycle(): float
    {
        $monthlyRecurring = $this->getBaseMonthlyRecurring();

        if ($this->billing_cycle === 'yearly') {
            $yearlyFull = $monthlyRecurring * 12;
            if ($this->apply_annual_discount) {
                $pct = (float) ($this->discount_percentage ?: 20.00);
                return round($yearlyFull * (1 - ($pct / 100)), 2);
            }
            return $yearlyFull;
        }

        return $monthlyRecurring;
    }

    public function getAnnualSavings(): float
    {
        if ($this->billing_cycle !== 'yearly' || !$this->apply_annual_discount) {
            return 0.0;
        }

        $yearlyFull = $this->getBaseMonthlyRecurring() * 12;
        $pct = (float) ($this->discount_percentage ?: 20.00);

        return round($yearlyFull * ($pct / 100), 2);
    }

    public function recalculateGrandTotal(): void
    {
        $setupFee = (float) ($this->setup_fee ?: 0);

        if ($this->isSubscription()) {
            $duration = (int) ($this->subscription_duration ?: 1);
            $recurring = $this->getRecurringPerCycle();

            $this->grand_total = $setupFee + ($recurring * $duration);
        } else {
            $this->grand_total = $setupFee + (float) $this->items()->sum('calculated_price');
        }

        $this->saveQuietly();
    }
}
