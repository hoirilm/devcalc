<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'module_id',
        'item_name',
        'base_price',
        'complexity_weight',
        'calculated_price',
    ];

    protected function casts(): array
    {
        return [
            'base_price' => 'decimal:2',
            'complexity_weight' => 'decimal:2',
            'calculated_price' => 'decimal:2',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }
}
