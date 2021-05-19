<?php

namespace App\Http\Controllers\Admin\Actions;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;

/**
 * Class GiftCodeValidation
 * @package App\Http\Controllers\Admin\Actions
 */
final class GiftCodeValidation
{

    public function __invoke(Request $request)
    {
        $code = Arr::get($request->all(), 'code');
        $giftId = Arr::get($request->all(), 'gift_id');

        if ($giftId) {
            return app('db')
                ->table('gift_cards_codes')
                ->where('codes', $code)
                ->where('gift_cards_id', '!=', $giftId)
                ->exists();
        } else {
            return app('db')
                ->table('gift_cards_codes')
                ->where('codes', $code)
                ->exists();
        }
    }

}
