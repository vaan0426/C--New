<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\PackagePurchase;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PackagePurchaseController extends Controller
{
    /**
     * Authenticated user's own package purchases (active/expired/exhausted).
     */
    public function index(Request $request)
    {
        return $request->user()->packagePurchases()->with('package')->latest()->get();
    }

    /**
     * Buy a package (spec §3.3): pay via Stripe or on-site.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'package_id' => ['required', 'exists:packages,id'],
            'method' => ['required', 'in:stripe,onsite'],
        ]);

        $package = Package::findOrFail($data['package_id']);
        $user = $request->user();

        return DB::transaction(function () use ($package, $user, $data) {
            $payment = Payment::create([
                'user_id' => $user->id,
                'amount' => $package->price,
                'currency' => 'EUR',
                'method' => $data['method'],
                'status' => Payment::STATUS_PENDING,
            ]);

            $purchase = PackagePurchase::create([
                'user_id' => $user->id,
                'package_id' => $package->id,
                'payment_id' => $payment->id,
                'sessions_remaining' => $package->sessions_count,
                'status' => PackagePurchase::STATUS_ACTIVE,
                'expires_at' => $package->validity_days
                    ? now()->addDays($package->validity_days)->toDateString()
                    : null,
            ]);

            return response()->json([
                'purchase' => $purchase->load('package'),
                'payment' => $payment,
            ], 201);
        });
    }
}
