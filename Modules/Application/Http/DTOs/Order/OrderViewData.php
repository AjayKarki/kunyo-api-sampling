<?php

namespace Modules\Application\Http\DTOs\Order;

use Foundation\Lib\Product;
use Modules\Payment\Payment;
use Modules\Payment\Libs\Payment as PaymentConst;
use Spatie\DataTransferObject\DataTransferObject;

final class OrderViewData extends DataTransferObject
{


	public ?int $id;

    public ?string $product_names;

	public ?string $transaction_id;

	public ?string $date;

	public ?string $payment_gateway;

	public ?int $order_count;

	public ?Float $amount;

	public ?string $payment_status;

    public ?Array $orders;

    public ?Array $metas;

    public ?Array $conversations;

	public static function fromModel(Payment $payment): DataTransferObject
    {

    	switch($payment->status) {
    		case(PaymentConst::PAYMENT_STATUS_PROCESSING) :
    			$status = 'PROCESSING';
    			break;
    		case(PaymentConst::PAYMENT_STATUS_DELIVERED) :
    			$status = 'PAID';
    			break;
            default:
    			$status = 'UNPAID';
    			break;
    	}

        return new self([
        	'id'				=> $payment->id,
            'product_names'     => $payment->product_names,
            'transaction_id'    => $payment->transaction_id,
            'date'              => optional($payment->created_at)->format('m M Y'),
            'payment_gateway'   => PaymentConst::gateways()[$payment->payment_gateway_id] ?? 'N/A',
            'order_count'		=> optional($payment->orders)->count() ?? 1,
            'amount'			=> optional($payment->orders)->map(function ($order) {
                                    return [ 'amount' => $order->amount * $order->quantity, ];
                                })->sum('amount'),
            'payment_status'    => $status,
            'orders'            => OrderViewData::resolveOrders($payment->orders),
            'metas'             => OrderViewData::resolveMetas($payment),

            'conversations'     => OrderViewData::resolveConversations(optional($payment)->conversations),
        ]);
    }

    private static function resolveOrders($orders): array
    {
        $resolveOrders = [];

        if ($orders) {

            foreach ($orders as $order) {
                $resolveOrders[] = [
                    'assigned_order_item' => $order->assigned_order_item,
                    'discounted_amount'   => $order->discounted_amount,
                    'order_code'          => $order->order_id,
                    'unit_price'          => number_format($order->amount, 2),
                    'quantity'            => $order->quantity,
                    'product_type'        => $order->order_type == \Foundation\Lib\Product::PRODUCT_GIFT_CARD_INDEX ? 'Gift Card' :  'Top Up',
                    'amount'              => number_format($order->quantity * $order->amount, 2),
                ];
            }

        }

        return $resolveOrders;
    }

    private static function resolveMetas($payment): array
    {

        $resolvedMetas = [];

        $total = optional($payment->orders)->map(function ($order) {
                return [ 'amount' => $order->amount * $order->quantity, ];
            })->sum('amount') ?? 0;

        $discountedAmt = $total - optional($payment->orders)->map(function ($order) {
                return [ 'amount' => $order->discounted_amount, ];
            })->sum('amount') ?? 0;

        $deliveryStatus = optional($payment->orders)->where('delivery_status', '!=', \Foundation\Lib\Order::ORDER_COMPLETED_STATUS)->count() ? 'Processing' : 'Completed';

        if ($discountedAmt !== $total) {
            $subTotal = $total - ( $total - $discountedAmt);
        } else {
            $subTotal = number_format($total, 2);
        }

        if ($discountedAmt !== $total) {
            $discount = number_format($discountedAmt, 2);
        }

        if ($payment->voucher_discount) {
            $voucherDiscount = number_format($payment->voucher_discount, 2);
            $voucher = $payment->discount_voucher_code ?? 'N/A';
        }

        $resolvedMetas = [
            'subtotal'          => $subTotal,
            'discount'          => number_format($total - $discountedAmt, 2) ?? null,
            'service_charge'    => number_format($payment->service_charge, 2),
            'delivery_status'   => $deliveryStatus,
            'discount_voucher'  => [
                'discount'      => $voucherDiscount ?? null,
                'voucher'       => $voucher ?? 'N/A',
            ],
            'total'             => number_format(($total - ( $total - $discountedAmt) + ($payment->service_charge ?? 0) ?? 0) - $voucherDiscount, 2),
            'codes'             => optional($payment->orders)->pluck('assigned_order_item')->flatten(),
        ];

        return $resolvedMetas;
    }

    private static function resolveConversations($conversations): array
    {
        $resolveConversations = [];

        if ($conversations) {

            foreach ($conversations as $conversation) {
                $resolveConversations[] = [
                    'identifier'       => $conversation->id,
                    'author_name'      => OrderViewData::resolveAuthor(optional($conversation)->author),
                    'author_display'   => optional($conversation->author)->getProfilePicture(),
                    'date'             => OrderViewData::resolveConversationDate(optional($conversation)->created_at),
                    'message'          => $conversation->message,
                    'is_new'           => !$conversation->acknowledged,
                ];
            }

        }

        return $resolveConversations;
    }

    private static function resolveAuthor($author): string
    {
        return optional($author)->hasRole('admin', 'super-admin') ? 'Kunyo.co' : 'You';
    }

    private static function resolveConversationDate($date): string
    {
        if ($date) {
            return optional($date)->isToday() ? optional($date)->format('h:i A') : optional($date)->format('d M, Y h:i A');
        }
        return 'N/A';
    }

}
