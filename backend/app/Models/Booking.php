<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'slot_id', 'help_type_id', 'package_purchase_id', 'status', 'price', 'public_note', 'internal_note'])]
class Booking extends Model
{
    use HasFactory;

    const STATUS_PENDING = 'pending';

    const STATUS_CONFIRMED = 'confirmed';

    const STATUS_REJECTED = 'rejected';

    const STATUS_RESCHEDULED = 'rescheduled';

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
        ];
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return BelongsTo<Slot, $this> */
    public function slot(): BelongsTo
    {
        return $this->belongsTo(Slot::class);
    }

    /** @return BelongsTo<HelpType, $this> */
    public function helpType(): BelongsTo
    {
        return $this->belongsTo(HelpType::class);
    }

    /** @return BelongsTo<PackagePurchase, $this> */
    public function packagePurchase(): BelongsTo
    {
        return $this->belongsTo(PackagePurchase::class);
    }
}
