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
        'user_id',
        'client_name',
        'currency_code',
        'exchange_rate',
        'grand_total',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'exchange_rate' => 'decimal:2',
            'grand_total' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(ProjectItem::class, 'project_id');
    }

    public function recalculateGrandTotal(): void
    {
        $this->grand_total = $this->items()->sum('calculated_price');
        $this->saveQuietly();
    }
}
