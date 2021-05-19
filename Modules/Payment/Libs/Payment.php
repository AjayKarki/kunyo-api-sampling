<?php

namespace Modules\Payment\Libs;

use Neputer\Supports\BaseModel;
use Neputer\Supports\BaseConstant;

final class Payment extends BaseConstant
{

    const PAYMENT_STATUS_PENDING = 0;
    const PAYMENT_STATUS_PROCESSING = 1;
    const PAYMENT_STATUS_CANCELLED = 2;
    const PAYMENT_STATUS_FAILED = 3;
    const PAYMENT_STATUS_DELIVERED = 4;
    const PAYMENT_STATUS_ERROR = 5;

    const PAYMENT_GATEWAY_COD = 0;
    const PAYMENT_GATEWAY_IMEPAY = 1;
    const PAYMENT_GATEWAY_ESEWA = 2;
    const PAYMENT_GATEWAY_CELLPAY = 3;
    const PAYMENT_GATEWAY_KHALTI = 4;
    const PAYMENT_GATEWAY_PRABHUPAY = 5;
    const PAYMENT_GATEWAY_OTHER = 6;
    const PAYMENT_GATEWAY_NICASIA = 7;

    const PAYMENT_STATUS_REMARKS_INITIATED = 'Transaction Initiated';
    const PAYMENT_STATUS_REMARKS_FAILED = 'Transaction Failed';
    const PAYMENT_STATUS_REMARKS_SUCCESS = 'Success';
    const PAYMENT_STATUS_REMARKS_NOT_FOUND = 'Not Found or Failed';

    const TRANSACTION_UNPAID_STATUS_INDEX = 0;
    const TRANSACTION_UNPAID_STATUS = 'UnPaid';
    const TRANSACTION_PAID_STATUS_INDEX = 1;
    const TRANSACTION_PAID_STATUS = 'Paid';

    public static function paymentStatus()
    {
        return [
            strtolower(self::TRANSACTION_UNPAID_STATUS) => self::TRANSACTION_UNPAID_STATUS,
            strtolower(self::TRANSACTION_PAID_STATUS) => self::TRANSACTION_PAID_STATUS,
            'processing' => 'Processing',
        ];
    }

    public static function gateways()
    {
        return [
            self::PAYMENT_GATEWAY_IMEPAY => 'ImePay',
            self::PAYMENT_GATEWAY_KHALTI => 'Khalti',
            self::PAYMENT_GATEWAY_PRABHUPAY => 'PrabhuPay',
            self::PAYMENT_GATEWAY_OTHER => 'Bank',
            self::PAYMENT_GATEWAY_NICASIA => 'Nic Asia',
//            self::PAYMENT_GATEWAY_CELLPAY => 'Cell Pay',
            self::PAYMENT_GATEWAY_ESEWA => 'Esewa',
//            self::PAYMENT_GATEWAY_COD => 'COD',
        ];
    }

    public static function imePayStatus()
    {
        return [
            self::PAYMENT_STATUS_DELIVERED,
            self::PAYMENT_STATUS_FAILED,
            self::PAYMENT_STATUS_ERROR,
            self::PAYMENT_STATUS_CANCELLED,
        ];
    }

}
