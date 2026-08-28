<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Deal extends Model
{
    use HasFactory;

    public const STAGES = [
        'discovery' => [
            'label' => 'Discovery',
            'probability' => 15,
            'color' => 'indigo',
            'bg' => 'bg-indigo-500',
        ],
        'scoping' => [
            'label' => 'Requirement Scoping',
            'probability' => 35,
            'color' => 'blue',
            'bg' => 'bg-blue-500',
        ],
        'proposal_sent' => [
            'label' => 'Proposal Sent',
            'probability' => 60,
            'color' => 'amber',
            'bg' => 'bg-amber-500',
        ],
        'negotiation' => [
            'label' => 'Negotiation',
            'probability' => 80,
            'color' => 'purple',
            'bg' => 'bg-purple-500',
        ],
        'won' => [
            'label' => 'Deal Won',
            'probability' => 100,
            'color' => 'emerald',
            'bg' => 'bg-emerald-500',
        ],
        'lost' => [
            'label' => 'Deal Lost',
            'probability' => 0,
            'color' => 'rose',
            'bg' => 'bg-rose-500',
        ],
    ];

    protected $fillable = [
        'user_id',
        'client_id',
        'title',
        'stage',
        'expected_value',
        'probability',
        'expected_close_date',
        'lost_reason',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'expected_value' => 'decimal:2',
            'probability' => 'integer',
            'expected_close_date' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class)->latest();
    }

    public function activities(): HasMany
    {
        return $this->hasMany(DealActivity::class)->latest('performed_at');
    }

    public function getStageInfo(): array
    {
        return self::STAGES[$this->stage] ?? [
            'label' => ucfirst($this->stage),
            'probability' => 20,
            'color' => 'slate',
            'bg' => 'bg-slate-500',
        ];
    }

    public function getWeightedValue(): float
    {
        return round(((float) $this->expected_value) * ($this->probability / 100), 2);
    }
}
