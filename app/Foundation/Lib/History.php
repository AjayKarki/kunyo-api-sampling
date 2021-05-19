<?php

namespace App\Foundation\Lib;

final class History
{
    const TYPE_VIEW = 1;
    const TYPE_UPDATE = 2;
    const TYPE_DELETE = 3;
    const TYPE_PURCHASE = 4;
    const TYPE_AUTOMATIC_ASSIGN = 5;
    const TYPE_IMPERSONATION = 6;
    const TYPE_LOGIN = 7;
    const TYPE_USER_ROLE_UPDATED = 8;

    public static $type = [
        self::TYPE_VIEW             => 'Item is viewed',
        self::TYPE_UPDATE           => 'Item is Updated',
        self::TYPE_DELETE           => 'Item is Deleted',
        self::TYPE_PURCHASE         => 'Item is Purchased',
        self::TYPE_AUTOMATIC_ASSIGN => 'Item is Automatically Assigned',
        self::TYPE_IMPERSONATION    => 'User Impersonation',
        self::TYPE_LOGIN            => 'Login',
        self::TYPE_USER_ROLE_UPDATED            => 'User Role Updated',
    ];

    public static $icon = [
        self::TYPE_VIEW             => 'eye',
        self::TYPE_UPDATE           => 'arrow-up',
        self::TYPE_DELETE           => 'trash-o',
        self::TYPE_PURCHASE         => 'arrow-circle-o-right',
        self::TYPE_AUTOMATIC_ASSIGN => 'arrows',
        self::TYPE_IMPERSONATION    => 'user-secret',
        self::TYPE_LOGIN            => 'sign-in',
    ];

    public static $dateFilter = [
        'week'  => 'This Week',
        'month' => 'This Month',
        'today' => 'Today',
        'yesterday' => 'Yesterday',
    ];

}
