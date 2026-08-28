<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Contact extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'name',
        'title',
        'email',
        'phone',
        'is_primary',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'is_primary' => 'boolean',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Generate Direct WhatsApp Click-to-Chat URL.
     */
    public function getWhatsAppUrl(?string $customText = null): ?string
    {
        if (!$this->phone) {
            return null;
        }

        // Clean phone number (replace leading 0 with 62 for Indonesian numbers, strip spaces & dashes)
        $clean = preg_replace('/[^0-9]/', '', $this->phone);
        if (str_starts_with($clean, '0')) {
            $clean = '62' . substr($clean, 1);
        }

        $text = $customText ?: "Halo {$this->name}, salam dari tim DevCalc.";
        return "https://wa.me/{$clean}?text=" . urlencode($text);
    }
}
