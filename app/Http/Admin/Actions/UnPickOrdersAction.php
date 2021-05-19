<?php

namespace App\Http\Controllers\Admin\Actions;

use Illuminate\Http\Request;
use Modules\Payment\PaymentService;

/**
 * Class UnPickOrdersAction
 * @package App\Http\Controllers\Admin\Actions
 */
final class UnPickOrdersAction
{

    private $paymentService;

    public function __construct( PaymentService $paymentService )
    {
        $this->paymentService = $paymentService;
    }

    public function __invoke(Request $request)
    {

        app('db')
            ->table('transactions')
            ->whereIn('id', explode(',', $request->get('ids')))
//            ->whereNull('picked_by')
            ->update([ 'picked_by' => null, 'updated_at' => now(), ]);
//
//        flash('success', 'Records are updated successfully !');
//        return redirect()->back();
    }

}
