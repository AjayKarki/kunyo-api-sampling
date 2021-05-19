<?php


namespace App\Foundation\Lib;


final class TopupPlayerInformation
{
    const STATUS_REQUESTED = 0;
    const STATUS_SUBMITTED = 1;
    const STATUS_COMPLETE = 2;

    public static $status = [
        self::STATUS_REQUESTED => 'Re-requested | Not Submitted',
        self::STATUS_SUBMITTED => 'Player Information Re-submitted',
        self::STATUS_COMPLETE => 'Player Information Accepted/Complete'
    ];
}
