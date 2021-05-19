<?php

namespace App\Http\Controllers\Admin\Actions;

use Foundation\Lib\Product;
use Illuminate\Http\Request;
use Neputer\Supports\BaseController;

/**
 * Class ChangeProductStatus
 * @package App\Http\Controllers\Admin\Actions
 */
final class ChangeProductStatus extends BaseController
{

    /**
     * @param Request $request
     * @return mixed
     */
    public function __invoke(Request $request)
    {
        $status = false;

        $productSlug = $request->get('product-slug');
        $statusType  = $request->get('status-type');
        $statusValue  = $request->get('status-value');

        switch ($productSlug) {
            case Product::PRODUCT_TOP_UP:
                $table = 'game_top_ups';
                break;
            default:
                $table = 'gift_cards';
        }

        $builder = app('db')
            ->table($table)
            ->where('id', $request->get('product-id'));

        if ($statusType === 'status') {
            $status = $this->changeStatus( $builder, !$statusValue );
        }

        if ($statusType === 'order-status') {
            $status = $this->changeOrderStatus( $builder, !$statusValue );
        }

        return $this->responseOk(
            $status,
            $status ? 'You have successfully updated the status.' : 'You have failed to update the status.'
        );
    }

    /**
     * Change status for the given product
     *
     * @param $builder
     * @param $status
     * @return mixed
     */
    private function changeStatus( $builder, $status )
    {
        return $builder->update([
            'status' => $status,
        ]);
    }

    /**
     * Change order status for the given product
     *
     * @param $builder
     * @param $status
     * @return mixed
     */
    private function changeOrderStatus( $builder, $status )
    {
        return $builder->update([
            'is_order_disable' => $status,
        ]);
    }

}
