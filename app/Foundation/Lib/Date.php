<?php

namespace Foundation\Lib;

final class Date
{

    const DATE_TODAY = 0;
    const DATE_YESTERDAY = 1;
    const DATE_WEEK = 2;
    const DATE_MONTH = 3;
    const DATE_YEAR = 4;
    const DATE_ALL = 5;

    public static function all() : array
    {
        return [
            Date::DATE_TODAY => 'Today',
            Date::DATE_YESTERDAY => 'Yesterday',
            Date::DATE_WEEK => 'This Week',
            Date::DATE_MONTH => 'This Month',
            Date::DATE_YEAR => 'This Year',
            Date::DATE_ALL => 'All',
        ];
    }

}
