<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['editor_id', 'help_type_id', 'title', 'slug', 'description', 'starts_at', 'price', 'capacity', 'spots_taken', 'images', 'is_active'])]
class Event extends Model
{
    use HasFactory;

    protected $appends = ['spots_left'];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'price' => 'decimal:2',
            'images' => 'array',
            'is_active' => 'boolean',
        ];
    }

    /** @return BelongsTo<User, $this> */
    public function editor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'editor_id');
    }

    /** @return BelongsTo<HelpType, $this> */
    public function helpType(): BelongsTo
    {
        return $this->belongsTo(HelpType::class);
    }

    /** @return HasMany<EventTicket, $this> */
    public function tickets(): HasMany
    {
        return $this->hasMany(EventTicket::class);
    }

    public function getSpotsLeftAttribute(): int
    {
        return max(0, $this->capacity - $this->spots_taken);
    }

    public function hasAvailability(int $quantity = 1): bool
    {
        return $this->getSpotsLeftAttribute() >= $quantity;
    }
}
