<?php

namespace App\Services;

use App\Models\PromoCode;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class PromoCodeService
{
    /**
     * Validate a promo code for the given user.
     */
    public function validate(string $code, User $user): ?PromoCode
    {
        $promo = PromoCode::where('code', $code)
            ->where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->first();

        if (!$promo) {
            return null;
        }

        $alreadyUsed = DB::table('promo_code_user')
            ->where('promo_code_id', $promo->id)
            ->where('user_id', $user->id)
            ->exists();

        if ($alreadyUsed) {
            return null;
        }

        if ($promo->type === 'single_use' && $promo->uses_count > 0) {
            return null;
        }

        if (
            $promo->type === 'multi_use' &&
            !is_null($promo->max_uses) &&
            $promo->uses_count >= $promo->max_uses
        ) {
            return null;
        }

        return $promo;
    }

    /**
     * Apply a promo code to a user after purchase.
     */
    public function apply(PromoCode $promo, User $user, int $purchaseId): void
    {
        DB::table('promo_code_user')->insert([
            'promo_code_id' => $promo->id,
            'user_id' => $user->id,
            'purchase_id' => $purchaseId,
            'used_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $promo->increment('uses_count');

        // Auto-disable if limits are hit
        if (
            $promo->type === 'single_use' ||
            ($promo->type === 'multi_use' &&
                !is_null($promo->max_uses) &&
                $promo->uses_count >= $promo->max_uses)
        ) {
            $promo->update(['is_active' => false]);
        }
    }
}
