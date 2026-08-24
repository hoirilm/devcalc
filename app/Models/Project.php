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
        'client_name',
        'grand_total',
        'status',
        'billing_type',
        'subscription_basis',
        'billing_cycle',
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

    public function getRecurringPerCycle(): float
    {
        $itemsTotal = (float) $this->items()->sum('calculated_price');
        $userTotal = ((int) ($this->user_count ?: 0)) * ((float) ($this->price_per_user ?: 0));

        $monthlyRecurring = match ($this->subscription_basis) {
            'per_user' => $userTotal,
            'hybrid' => $itemsTotal + $userTotal,
            default => $itemsTotal, // 'modular'
        };

        return $this->billing_cycle === 'yearly' ? ($monthlyRecurring * 12) : $monthlyRecurring;
    }

    public function recalculateGrandTotal(): void
    {
        if ($this->isSubscription()) {
            $duration = (int) ($this->subscription_duration ?: 1);
            $setupFee = (float) ($this->setup_fee ?: 0);
            $recurring = $this->getRecurringPerCycle();

            $this->grand_total = $setupFee + ($recurring * $duration);
        } else {
            $this->grand_total = (float) $this->items()->sum('calculated_price');
        }

        $this->saveQuietly();
    }
}
