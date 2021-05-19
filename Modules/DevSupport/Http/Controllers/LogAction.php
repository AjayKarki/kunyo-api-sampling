<?php

namespace Modules\DevSupport\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Arcanedev\LogViewer\Contracts\LogViewer as LogViewerContract;

/**
 * Class LogAction
 * @package Modules\DevSupport\Http\Controllers
 */
final class LogAction
{

    /** @var int */
    protected $perPage = 30;

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

    public function __invoke(Request $request)
    {
        $data               = [];

        $data['statistics'] = $this->logViewer->statsTable();
        $data['headers']    = $data['statistics']->header();
        $data['rows']       = $this->paginate($data['statistics']->rows(), $request);

        return view('support::log', compact('data'));
    }

    /**
     * Paginate logs.
     *
     * @param array $data
     * @param Request $request
     *
     * @return LengthAwarePaginator
     */
    protected function paginate(array $data, Request $request)
    {
        $data = new Collection($data);
        $page = $request->get('page', 1);
        $path = $request->url();

        return new LengthAwarePaginator(
            $data->forPage($page, $this->getLimit()),
            $data->count(),
            $this->getLimit(),
            $page,
            compact('path')
        );
    }

    private function getLimit()
    {
        return config('support.perPage', $this->perPage);
    }

}
