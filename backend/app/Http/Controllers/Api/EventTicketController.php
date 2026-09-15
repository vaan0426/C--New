<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventTicket;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EventTicketController extends Controller
{
    public function index(Request $request)
    {
        return $request->user()->eventTickets()->with('event')->latest()->get();
    }

    /**
     * Buy event tickets — only while spots remain (spec §3.5, business rule 10).
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'event_id' => ['required', 'exists:events,id'],
            'quantity' => ['sometimes', 'integer', 'min:1'],
            'method' => ['required', 'in:stripe,onsite'],
        ]);

        $quantity = $data['quantity'] ?? 1;
        $user = $request->user();

        return DB::transaction(function () use ($data, $quantity, $user) {
            $event = Event::lockForUpdate()->findOrFail($data['event_id']);

            if (! $event->hasAvailability($quantity)) {
                abort(422, 'Няма достатъчно свободни места за това събитие.');
            }

            $payment = Payment::create([
                'user_id' => $user->id,
                'amount' => $event->price * $quantity,
                'currency' => 'EUR',
                'method' => $data['method'],
                'status' => Payment::STATUS_PENDING,
            ]);

            $ticket = EventTicket::create([
                'user_id' => $user->id,
                'event_id' => $event->id,
                'payment_id' => $payment->id,
                'quantity' => $quantity,
                'status' => EventTicket::STATUS_CONFIRMED,
            ]);

            $event->increment('spots_taken', $quantity);

            return response()->json([
                'ticket' => $ticket->load('event'),
                'payment' => $payment,
            ], 201);
        });
    }

    /**
     * Cancel a ticket — releases the spots back to the event.
     */
    public function destroy(EventTicket $eventTicket)
    {
        return DB::transaction(function () use ($eventTicket) {
            if ($eventTicket->status === EventTicket::STATUS_CONFIRMED) {
                $eventTicket->event()->decrement('spots_taken', $eventTicket->quantity);
            }

            $eventTicket->update(['status' => EventTicket::STATUS_CANCELLED]);

            return response()->json(status: 204);
        });
    }
}
