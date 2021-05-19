<?php

namespace Modules\DevSupport\Http\Controllers;

use Illuminate\Support\Arr;
use Arcanedev\LogViewer\Contracts\LogViewer as LogViewerContract;

/**
 * Class HomeAction
 * @package Modules\DevSupport\Http\Controllers
 */
class HomeAction
{

    private $logViewer;

    /**
     * HomeAction constructor.
     *
     * @param LogViewerContract $logViewer
     */
    public function __construct(LogViewerContract $logViewer)
    {
        $this->logViewer = $logViewer;
    }

    public function __invoke()
    {
        $data                = [];

        $data['statistics']  = $this->logViewer->statsTable();
        $data['percents']    = $this->calcPercentages($data['statistics']->footer(), $data['statistics']->header());

        return view('support::dashboard', compact('data'));
    }

    /**
     * Calculate the percentage.
     *
     * @param  array  $total
     * @param  array  $names
     *
     * @return array
     */
    protected function calcPercentages(array $total, array $names)
    {
        $percents = [];
        $all      = Arr::get($total, 'all');

        foreach ($total as $level => $count) {
            $percents[$level] = [
                'name'    => $names[$level],
                'count'   => $count,
                'percent' => $all ? round(($count / $all) * 100, 2) : 0,
            ];
        }

        return $percents;
    }

}
