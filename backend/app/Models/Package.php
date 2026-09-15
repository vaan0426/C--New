<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'slug', 'description', 'sessions_count', 'price', 'validity_days', 'is_featured', 'is_active'])]
class Package extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    /** @return HasMany<PackagePurchase, $this> */
    public function purchases(): HasMany
    {
        return $this->hasMany(PackagePurchase::class);
    }
}
