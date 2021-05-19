<?php


namespace Foundation\Lib;


/**
 * Class ExpenseCategory
 * @package Foundation\Lib
 */
final class ExpenseCategory
{
    const PAYMENT_WITH_POS  = 1;
    const PAYMENT_WITH_CHEQUE  = 2;
    const PAYMENT_WITH_CASH  = 3;
    const PAYMENT_WITH_BANK = 4;
    const PAYMENT_WITH_ONLINE = 5;
    const PAYMENT_WITH_OTHER = 6;

    public static $payment = [
        self::PAYMENT_WITH_POS => 'POS',
        self::PAYMENT_WITH_CHEQUE => 'Cheque',
        self::PAYMENT_WITH_CASH => 'Cash',
        self::PAYMENT_WITH_BANK => 'Bank Transfer',
        self::PAYMENT_WITH_ONLINE => 'Online Transfer',
        self::PAYMENT_WITH_OTHER => 'Other',
    ];

    public static $gateway = [
        'Esewa' => 'Esewa',
        'Khalti' => 'Khalti',
        'Prabhu Pay' => 'Prabhu Pay',
        'IME Pay' => 'IME Pay',
        'Connect IPS' => 'Connect IPS',
    ];
}
