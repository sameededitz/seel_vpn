<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\Plan;
use App\Models\PromoCode;
use Illuminate\Http\Request;
use App\Models\StripeSession;
use Illuminate\Support\Facades\DB;
use App\Jobs\SendEmailVerification;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\PurchaseResource;
use Illuminate\Support\Facades\Validator;

class PurchaseController extends Controller
{
    public function addPurchase(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'plan_id' => 'required|exists:plans,id',
            'promo_code' => 'nullable|string|max:50',
            'payment_intent' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->all(),
            ], 400);
        }

        $paymentIntent = $request->payment_intent;
        if ($paymentIntent) {
            $existingSession = StripeSession::where('payment_intent', $paymentIntent)->first();

            if ($existingSession) {
                return response()->json([
                    'status' => true,
                    'message' => 'Purchase already processed!',
                ], 200);
            }
        }


        $promo = null;
        $discountPercent = 0;
        $promoCode = $request->input('promo_code');

        if ($promoCode) {
            $promo = PromoCode::where('code', $promoCode)
                ->where('is_active', true)
                ->unused()
                ->notExpired()
                ->first();

            if (!$promo) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid or expired promo code.',
                ], 400);
            }

            $discountPercent = $promo->discount_percent;
        }

        $user = Auth::user();
        /** @var \App\Models\User $user **/

        $plan = Plan::findOrFail($request->plan_id);

        // Determine the price to use
        $price = $plan->discount_price ?? $plan->original_price;

        if ($discountPercent > 0) {
            $price -= round(($price * $discountPercent) / 100, 2);
        }

        try {
            DB::beginTransaction();

            /** @var \App\Models\Purchase $purchase **/
            $purchase = $user->purchases()
                ->where('status', 'active')
                ->where('end_date', '>', now())
                ->first();

            $duration = $plan->duration;

            if ($purchase) {
                $newEndDate = $this->calculateExpiration(
                    Carbon::parse($purchase->end_date),
                    $plan->duration,
                    $plan->duration_unit
                );

                // Update the purchase with the new expiration date
                $purchase->update([
                    'plan_id' => $plan->id,
                    'end_date' => $newEndDate,
                    'status' => 'active',
                    'amount_paid' => $purchase->amount_paid + $price,
                ]);

                $message = 'Purchase Extended successfully!';
            } else {
                $expiresAt = $this->calculateExpiration(now(), $duration, $plan->duration_unit);
                // Create a new purchase
                $purchase = $user->purchases()->create([
                    'plan_id' => $plan->id,
                    'amount_paid' => $price,
                    'start_date' => now(),
                    'end_date' => $expiresAt,
                    'status' => 'active',
                ]);

                $message = 'Purchase created successfully!';
            }

            if ($promo) {
                $promo->update([
                    'is_active' => false,
                    'user_id' => $user->id,
                    'used_at' => now(),
                    'purchase_id' => $purchase->id,
                ]);
            }

            if ($paymentIntent) {
                StripeSession::create([
                    'payment_intent' => $paymentIntent,
                    'user_id' => $user->id,
                    'purchase_id' => $purchase->id,
                ]);
            }

            if (!$user->hasVerifiedEmail()) {
                SendEmailVerification::dispatch($user)->delay(now()->addSeconds(5));
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => $message,
                'purchase' => new PurchaseResource($purchase->load('plan', 'user')),
            ], 200);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Something went wrong while processing the purchase.',
                'error' => $e->getMessage(), // Optional: remove in production
            ], 500);
        }
    }

    public function stripeSession(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'payment_intent' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->all(),
            ], 400);
        }

        $paymentIntent = StripeSession::where('payment_intent', $request->payment_intent)->first();

        return response()->json([
            'status' => true,
            'is_used' => (bool) $paymentIntent,
            'payment_intent' => $request->payment_intent,
            'message' => $paymentIntent ? 'Payment intent found.' : 'Payment intent not found.',
        ], 200);
    }

    public function active()
    {
        /** @var \App\Models\User $user **/
        $user = Auth::user();
        $activePlan = $user->purchases()
            ->where('status', 'active')
            ->where('end_date', '>', now())
            ->with('plan', 'user')
            ->first();

        if (!$activePlan) {
            return response()->json([
                'status' => false,
                'message' => 'No active plan found.'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'Active plan found.',
            'plan' => new PurchaseResource($activePlan),
        ], 200);
    }

    public function viewPurchase($id)
    {
        /** @var \App\Models\User $user **/
        $user = Auth::user();
        $purchase = $user->purchases()
            ->where('id', $id)
            ->with('plan', 'user')
            ->first();

        if (!$purchase) {
            return response()->json([
                'status' => false,
                'message' => 'Purchase not found.'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'Purchase found.',
            'purchase' => new PurchaseResource($purchase),
        ], 200);
    }

    public function history()
    {
        /** @var \App\Models\User $user **/
        $user = Auth::user();
        $purchases = $user->purchases()->with('plan', 'user')->latest()->get();
        return response()->json([
            'status' => true,
            'purchases' => PurchaseResource::collection($purchases),
            'message' => 'Purchase history retrieved successfully.',
        ], 200);
    }

    public function apply(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->all(),
            ], 400);
        }

        $promo = PromoCode::where('code', $request->code)
            ->where('is_active', true)
            ->whereNull('user_id')
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->first();

        if (!$promo) {
            return response()->json(['message' => 'Invalid or expired promo code.'], 404);
        }

        return response()->json([
            'message' => 'Promo code valid.',
            'discount_percent' => $promo->discount_percent,
        ]);
    }

    private function calculateExpiration($startDate, $duration, $unit)
    {
        return match ($unit) {
            'day'   => $startDate->addDays($duration),
            'week'  => $startDate->addWeeks($duration),
            'month' => $startDate->addMonths($duration),
            'year'  => $startDate->addYears($duration),
            default => $startDate->addDays(7),
        };
    }
}
