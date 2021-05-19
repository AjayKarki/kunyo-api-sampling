<?php

namespace Modules\Application\Http\DTOs\Order;

use Foundation\Lib\Product;
use Modules\Payment\Payment;
use Modules\Payment\Libs\Payment as PaymentConst;
use Spatie\DataTransferObject\DataTransferObject;

final class OrderData extends DataTransferObject
{

	public ?int $id;

	public ?string $transaction_id;

	public ?string $date;

	public ?string $payment_gateway;

	public ?int $order_count;

	public ?Float $amount;

	public ?string $payment_status;

	public static function fromModel(Payment $payment): DataTransferObject
    {

    	switch($payment->status) {
    		case(PaymentConst::PAYMENT_STATUS_PENDING) :
    			$status = 'UNPAID';
    			break;
    		case(PaymentConst::PAYMENT_STATUS_PROCESSING) :
    			$status = 'PROCESSING';
    			break;
    		case(PaymentConst::PAYMENT_STATUS_DELIVERED) :
    			$status = 'PAID';
    			break;
    		case(PaymentConst::PAYMENT_STATUS_PENDING) :
    			$status = 'UNPAID';
    			break;
    	}

        return new self([
        	'id'				=> $payment->id,
            'transaction_id'    => $payment->transaction_id,
            'date'              => optional($payment->created_at)->format('d M Y'),
            'payment_gateway'   => PaymentConst::gateways()[$payment->payment_gateway_id] ?? 'N/A',
            'order_count'		=> optional($payment->orders)->count() ?? 1,
            'amount'			=> optional($payment->orders)->map(function ($order) {
                                    return [ 'amount' => $order->amount * $order->quantity, ];
                                })->sum('amount'),
            'payment_status'    => $status
        ]);
    }

}
