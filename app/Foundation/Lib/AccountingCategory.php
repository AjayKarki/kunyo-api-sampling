<?php


namespace Foundation\Lib;


class AccountingCategory
{
    const TYPE_INCOME = 1;
    const TYPE_EXPENSE = 2;

    public static $type = [
        self::TYPE_INCOME => 'Income',
        self::TYPE_EXPENSE => 'Expense'
    ];

    public static $typeL = [
        self::TYPE_INCOME => 'income',
        self::TYPE_EXPENSE => 'expense'
    ];

}
