<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Module extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'base_price',
        'subscription_price',
        'category',
    ];

    protected function casts(): array
    {
        return [
            'base_price' => 'decimal:2',
            'subscription_price' => 'decimal:2',
        ];
    }

    public function projectItems(): HasMany
    {
        return $this->hasMany(ProjectItem::class);
    }
}
