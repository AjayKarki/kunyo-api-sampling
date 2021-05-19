<?php

namespace App\Http\Controllers\Admin\Order;

use Foundation\Lib\Product;
use Foundation\Models\Order;
use Foundation\Resolvers\NotifyResolver;
use Illuminate\Http\Request;
use Modules\Payment\Payment;

class RedeemVerifyAction
{

    /**
     * @param Payment $transaction
     * @return array|string
     */
    public function __invoke(Payment $transaction)
    {
        $notify = $transaction->redeem;

        $transaction->update([
            'redeem' => 0,
        ]);

        if ($notify) {
            NotifyResolver::notify($transaction, 'redeemed', false, 'sms');
        }
        flash('success', 'You have successfully redeemed the orders.');
        return back();
    }

}
