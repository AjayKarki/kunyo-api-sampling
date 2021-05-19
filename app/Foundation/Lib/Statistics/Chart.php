<?php

namespace Foundation\Lib\Statistics;

/**
 * Class Chart
 * @package Foundation\Lib\Statistics
 */
final class Chart
{

    /**
     * @param $columns
     * @param $colors
     * @return object
     */
    public static function getPieChart($columns, $colors)
    {
        return (object) [
            'columns' => $columns,
            'colors'  => $colors,
        ];
    }

    public static function getLineChart($chartData, $columns, $colors)
    {
        return (object) [
            'data'    => $chartData,
            'columns' => $columns,
            'colors'  => $colors,
        ];
    }

}
