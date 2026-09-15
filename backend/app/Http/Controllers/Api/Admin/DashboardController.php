<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Event;
use App\Models\Payment;
use App\Models\Slot;

class DashboardController extends Controller
{
    /**
     * Summary counts for the admin dashboard (spec §6).
     */
    public function index()
    {
        return response()->json([
            'pending_bookings' => Booking::where('status', Booking::STATUS_PENDING)->count(),
            'upcoming_slots' => Slot::available()->count(),
            'upcoming_events' => Event::where('is_active', true)->where('starts_at', '>=', now())->count(),
            'payments_pending' => Payment::where('status', Payment::STATUS_PENDING)->count(),
            'revenue_paid' => (float) Payment::where('status', Payment::STATUS_PAID)->sum('amount'),
        ]);
    }
}
