<?php


namespace App\Foundation\Lib;


final class G2AProductKeys
{
    const STATUS_ORDERED = 1;
    const STATUS_PAID = 2;
    const STATUS_IMPORTED = 3;
    const STATUS_COMPLETED = 4;

    static $status = [
        self::STATUS_ORDERED => 'Order Placed',
        self::STATUS_PAID => 'Paid for the Key',
        self::STATUS_IMPORTED => 'Key Imported',
        self::STATUS_COMPLETED => 'Key Imported | Complete'
    ];
}
