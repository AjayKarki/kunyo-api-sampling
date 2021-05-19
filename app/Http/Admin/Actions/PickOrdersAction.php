<?php

namespace App\Http\Controllers\Admin\Actions;

use Foundation\Resolvers\Order\OrderAmountResolver;
use Foundation\Services\AssignAmountService;
use Illuminate\Http\Request;
use Modules\Payment\PaymentService;
use Neputer\Supports\Mixins\Responsable;

/**
 * Class PickOrdersAction
 * @package App\Http\Controllers\Admin\Actions
 */
final class PickOrdersAction
{
    use Responsable;

    private $paymentService;

    public function __construct( PaymentService $paymentService )
    {
        $this->paymentService = $paymentService;
    }

    public function __invoke(Request $request)
    {
        $ids = explode(',', $request->get('ids'));

        $alreadyPicked = app('db')
            ->table('transactions')
            ->whereIn('id', $ids)
            ->whereNotNull('picked_by')
            ->pluck('transaction_id');

        app('db')
            ->table('transactions')
            ->whereIn('id', $ids)
            ->whereNull('picked_by')
            ->update([ 'picked_by' => auth()->id(), 'updated_at' => now(), ]);

        return $this->responseOk($alreadyPicked->isEmpty() ? false : $alreadyPicked->implode(','));
    }

}
