<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Stripe\Webhook;

class PaymentController extends Controller
{
    /**
     * Create a Stripe Checkout session for a pending payment (spec §8: Stripe
     * for reservations and events; cart flow finishes with Stripe checkout).
     */
    public function createCheckoutSession(Request $request, Payment $payment)
    {
        if ($payment->user_id !== $request->user()->id) {
            abort(403);
        }

        if ($payment->status !== Payment::STATUS_PENDING) {
            return response()->json(['message' => 'Плащането вече е обработено.'], 422);
        }

        Stripe::setApiKey(config('services.stripe.secret'));

        $session = Session::create([
            'mode' => 'payment',
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => strtolower($payment->currency),
                    'product_data' => ['name' => 'Психологически програми — плащане #'.$payment->id],
                    'unit_amount' => (int) round($payment->amount * 100),
                ],
                'quantity' => 1,
            ]],
            'success_url' => config('app.frontend_url').'/checkout/success?payment='.$payment->id,
            'cancel_url' => config('app.frontend_url').'/checkout/cancel?payment='.$payment->id,
            'metadata' => ['payment_id' => $payment->id],
        ]);

        $payment->update(['stripe_payment_intent_id' => $session->payment_intent]);

        return response()->json(['checkout_url' => $session->url]);
    }

    /**
     * Stripe webhook: mark the payment as paid once Stripe confirms it.
     */
    public function webhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $secret = config('services.stripe.webhook_secret');

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $secret);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Invalid webhook signature.'], 400);
        }

        if ($event->type === 'checkout.session.completed') {
            $session = $event->data->object;
            $paymentId = $session->metadata->payment_id ?? null;

            if ($paymentId && ($payment = Payment::find($paymentId))) {
                $payment->update(['status' => Payment::STATUS_PAID]);
            }
        }

        return response()->json(['received' => true]);
    }

    /**
     * Admin/editor: mark an on-site payment as received.
     */
    public function markPaid(Payment $payment)
    {
        $payment->update(['status' => Payment::STATUS_PAID]);

        return $payment;
    }
}
