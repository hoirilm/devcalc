<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'industry',
        'email',
        'phone',
        'website',
        'address',
        'status',
        'notes',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(Contact::class)->orderByDesc('is_primary');
    }

    public function primaryContact(): ?Contact
    {
        return $this->contacts()->where('is_primary', true)->first() ?: $this->contacts()->first();
    }

    public function deals(): HasMany
    {
        return $this->hasMany(Deal::class)->latest();
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class)->latest();
    }

    public function activities(): HasMany
    {
        return $this->hasMany(DealActivity::class)->latest('performed_at');
    }

    /**
     * Calculate Total Lifetime Value (LTV) from all won deals or official generated projects.
     */
    public function getTotalLtv(): float
    {
        return (float) $this->projects()
            ->where('status', '!=', 'Draft')
            ->sum('grand_total');
    }

    /**
     * Total quotations count.
     */
    public function getProjectsCount(): int
    {
        return $this->projects()->count();
    }

    /**
     * Active deals count.
     */
    public function getActiveDealsCount(): int
    {
        return $this->deals()->whereNotIn('stage', ['won', 'lost'])->count();
    }
}
