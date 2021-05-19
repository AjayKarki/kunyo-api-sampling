<?php

namespace Foundation\Resolvers;

use Throwable;

/**
 * Class EmailOrderResolver
 * @package Foundation\Resolvers
 */
final class EmailOrderResolver
{

    /**
     * Resolve the email template for order delivered
     *
     * @param $data
     * @return array|string
     * @throws Throwable
     */
    public static function resolveDeliveredOrder($data)
    {
        return view('email.template-order-delivered', compact('data'))->render();
    }

    /**
     * Resolve the email template for order pending
     *
     * @param $data
     * @return array|string
     * @throws Throwable
     */
    public static function resolvePendingOrder($data)
    {
        return view('email.template-order-pending', compact('data'))->render();
    }

}
