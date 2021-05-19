<?php

namespace Foundation\Resolvers\Voucher;

use App\Foundation\Lib\DiscountVoucher;

/**
 * Class VoucherResolver
 * @package Foundation\Resolvers\Voucher
 */
final class VoucherResolver
{

    /**
     * $voucherCode ie the code
     * $totalPrice is the discounted price (including service charge and all and excluding the voucher)
     * $applied How many times the voucher is applied
     */
    public static function apply($voucherCode, $totalPrice, $applied)
    {
        $discountedPrice = 0;

        $discountVoucher = static::get($voucherCode);

        if ($discountVoucher) {
            $exists = static::exists($discountVoucher);

            if ($exists && static::isApplicable($totalPrice, $discountVoucher, $applied)) {
                $discountedPrice = static::resolveDiscountedPrice($discountVoucher, $totalPrice, $applied);
            } else {
                return false;
            }
        }

        return [
            'discount_voucher_id' => optional($discountVoucher)->id,
            'code'                => optional($discountVoucher)->voucher,
            'total_remaining'     => optional($discountVoucher)->total_remaining,
            'discounted_price'    => $discountedPrice,
            'minimum_order_amt'   => optional($discountVoucher)->min_order_amount,
            'total_used'          => (optional($discountVoucher)->use_count ?? 0) + $applied,
        ];
    }

    private static function resolveDiscountedPrice($discountVoucher, $totalPrice, $applied)
    {
        $discountAmount = $discountVoucher->discount_amount;

        if ($discountVoucher->type == DiscountVoucher::TYPE_PERCENT) {
            $discountAmount = $totalPrice * $discountVoucher->discount_percent / 100;
        }

        return $discountAmount * $applied;
    }

    private static function isApplicable($totalPrice, $discountVoucher, $applied)
    {
        $totalPrice = static::toInt($totalPrice);
        $minOrderAmount = static::toInt($discountVoucher->min_order_amount);
        $resolveDiscountedPrice = static::toInt(static::resolveDiscountedPrice($discountVoucher, $totalPrice, $applied));
        return $totalPrice >= $minOrderAmount && $totalPrice > $resolveDiscountedPrice;
    }

    private static function exists($discountVoucher)
    {
        $exists = false;

        if (!static::isMaxedOut($discountVoucher)) {
            $exists = static::isGlobal($discountVoucher) ? true : $discountVoucher->user_id === auth()->id();
        }

        return $exists;
    }

    private static function isGlobal($discountVoucher)
    {
        $isGlobal = false;

        if ($discountVoucher) {
            $isGlobal = is_null($discountVoucher->user_id);
        }

        return $isGlobal;
    }

    private static function isMaxedOut($discountVoucher)
    {
        $isMaxedOut = false;

        if ($discountVoucher) {
            $isMaxedOut = $discountVoucher->use_count == $discountVoucher->max_use;
        }
        return $isMaxedOut;
    }

    public static function get($voucherCode)
    {
        return app('db')
            ->table('discount_vouchers')
            ->select('*')
            ->where('voucher', $voucherCode)
            ->selectraw('max_use - use_count AS total_remaining')
            ->whereRaw('(now() between start_date and end_date)')
            ->whereColumn('max_use', '!=','use_count')
            ->where('status', 1)
            ->first();
    }

    public static function updateVoucher($voucherCode, $applied)
    {
        if ($voucher = static::get($voucherCode)) {
            app('db')
                ->table('discount_vouchers')
                ->select('*')
                ->where('voucher', $voucherCode)
                ->selectraw('max_use - use_count AS total_remaining')
                ->whereRaw('(now() between start_date and end_date)')
                ->whereColumn('max_use', '!=','use_count')
                ->where('status', 1)
                ->update([
                    'use_count' => $voucher->use_count + $applied,
                ]);
        }
    }

    public static function isApplyDiscountViewable()
    {
        $voucher = app('db')
            ->table('discount_vouchers')
            ->select('user_id')
            ->whereRaw('(now() between start_date and end_date)')
            ->whereColumn('max_use', '!=','use_count')
            ->selectRaw('max_use - use_count AS total_remaining')
            ->where('status', 1)
            ->where('user_id', auth()->id())
            ->first();

        if (is_null($voucher)) {
            return false;
        }

        if ($voucher->user_id) {
            return $voucher->user_id === auth()->id() && $voucher->total_remaining > 0;
        }
        return $voucher->total_remaining > 0;
    }

    /**
     * Comma separated price to float
     */
    public static function toInt($str)
    {
        return (float) str_replace(',', '', $str);
        // return intval(str_replace(",", "", $str);
    }

}
