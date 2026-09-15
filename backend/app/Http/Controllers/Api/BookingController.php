<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\HelpType;
use App\Models\PackagePurchase;
use App\Models\Slot;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    /**
     * Authenticated user's own bookings (history + upcoming), spec §5.3.
     */
    public function index(Request $request)
    {
        return $request->user()
            ->bookings()
            ->with(['slot.editor:id,name', 'helpType'])
            ->latest()
            ->get();
    }

    /**
     * Admin/editor: all booking requests, filterable by status (spec §6).
     */
    public function adminIndex(Request $request)
    {
        $query = Booking::with(['user:id,name,email,phone', 'slot.editor:id,name', 'helpType']);

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        return $query->latest()->get();
    }

    /**
     * Create a booking request. Supports both a logged-in user (§4.2) and a
     * guest who is registered on the fly (§4.1) — either way the request is
     * created as "pending" and the slot is provisionally reserved.
     */
    public function store(Request $request)
    {
        $rules = [
            'slot_id' => ['required', 'exists:slots,id'],
            'help_type_id' => ['required', 'exists:help_types,id'],
            'package_purchase_id' => ['nullable', 'exists:package_purchases,id'],
            'public_note' => ['nullable', 'string', 'max:2000'],
        ];

        $user = $request->user('sanctum');

        if (! $user) {
            $rules['name'] = ['required', 'string', 'max:255'];
            $rules['email'] = ['required', 'email', 'max:255', 'unique:users,email'];
            $rules['phone'] = ['nullable', 'string', 'max:50'];
        }

        $data = $request->validate($rules);

        return DB::transaction(function () use ($data, $user) {
            $slot = Slot::lockForUpdate()->findOrFail($data['slot_id']);

            if ($slot->is_booked) {
                abort(422, 'Този час вече не е свободен.');
            }

            if (! $user) {
                $user = User::create([
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'phone' => $data['phone'] ?? null,
                    'password' => Hash::make(Str::random(32)),
                    'role' => User::ROLE_USER,
                    'status' => User::STATUS_ACTIVE,
                ]);

                event(new Registered($user));
            }

            if ($user->isFrozen()) {
                abort(403, 'Профилът е замразен — нови заявки не са позволени.');
            }

            $helpType = HelpType::findOrFail($data['help_type_id']);

            $packagePurchase = null;
            if (! empty($data['package_purchase_id'])) {
                $packagePurchase = PackagePurchase::where('id', $data['package_purchase_id'])
                    ->where('user_id', $user->id)
                    ->first();

                if (! $packagePurchase || ! $packagePurchase->hasSessionsAvailable()) {
                    abort(422, 'Избраният пакет няма налични часове.');
                }
            }

            $booking = Booking::create([
                'user_id' => $user->id,
                'slot_id' => $slot->id,
                'help_type_id' => $helpType->id,
                'package_purchase_id' => $packagePurchase?->id,
                'status' => Booking::STATUS_PENDING,
                'price' => $packagePurchase ? 0 : $helpType->base_price,
                'public_note' => $data['public_note'] ?? null,
            ]);

            $slot->update(['is_booked' => true]);

            return $booking->load(['slot.editor:id,name', 'helpType']);
        });
    }

    /**
     * Admin/editor confirms a pending request (spec §7.3-4).
     */
    public function confirm(Booking $booking)
    {
        return DB::transaction(function () use ($booking) {
            if ($booking->status === Booking::STATUS_CONFIRMED) {
                return $booking;
            }

            $booking->update(['status' => Booking::STATUS_CONFIRMED]);

            if ($booking->packagePurchase) {
                $purchase = $booking->packagePurchase()->lockForUpdate()->first();
                $purchase->decrement('sessions_remaining');

                if ($purchase->sessions_remaining <= 0) {
                    $purchase->update(['status' => PackagePurchase::STATUS_EXHAUSTED]);
                }
            }

            return $booking->fresh(['slot.editor:id,name', 'helpType', 'user:id,name,email']);
        });
    }

    /**
     * Admin/editor rejects a pending request; frees the slot back up.
     */
    public function reject(Booking $booking)
    {
        return DB::transaction(function () use ($booking) {
            $booking->update(['status' => Booking::STATUS_REJECTED]);
            $booking->slot->update(['is_booked' => false]);

            return $booking;
        });
    }

    /**
     * Admin/editor moves the booking to a different free slot (spec §3.2, §5.1).
     */
    public function reschedule(Request $request, Booking $booking)
    {
        $data = $request->validate([
            'slot_id' => ['required', 'exists:slots,id'],
        ]);

        return DB::transaction(function () use ($data, $booking) {
            $newSlot = Slot::lockForUpdate()->findOrFail($data['slot_id']);

            if ($newSlot->is_booked) {
                abort(422, 'Избраният нов час вече не е свободен.');
            }

            $oldSlot = $booking->slot;
            $booking->update(['slot_id' => $newSlot->id, 'status' => Booking::STATUS_RESCHEDULED]);
            $newSlot->update(['is_booked' => true]);
            $oldSlot->update(['is_booked' => false]);

            return $booking->fresh(['slot.editor:id,name', 'helpType']);
        });
    }

    /**
     * Admin/editor: add or update the internal-only note (spec §5.2).
     */
    public function updateInternalNote(Request $request, Booking $booking)
    {
        $data = $request->validate([
            'internal_note' => ['nullable', 'string', 'max:4000'],
        ]);

        $booking->update($data);

        return $booking;
    }
}
