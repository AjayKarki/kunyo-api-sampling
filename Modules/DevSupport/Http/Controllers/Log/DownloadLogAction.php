<?php

namespace Modules\DevSupport\Http\Controllers\Log;

use Arcanedev\LogViewer\Contracts\LogViewer as LogViewerContract;

/**
 * Class DownloadLogAction
 * @package Modules\DevSupport\Http\Controllers\Log
 */
class DownloadLogAction
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

    public function __invoke($date)
    {
        return $this->logViewer->download($date);
    }

}
