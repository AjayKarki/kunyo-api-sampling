<?php

namespace Modules\DevSupport\Http\Controllers\Log;

use Illuminate\Http\Request;
use Arcanedev\LogViewer\Contracts\LogViewer as LogViewerContract;

/**
 * Class DeleteLogAction
 * @package Modules\DevSupport\Http\Controllers\Log
 */
final class DeleteLogAction
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

    public function __invoke(Request $request)
    {
        abort_unless($request->ajax(), 405, 'Method Not Allowed');

        $date = $request->get('date');

        return response()->json([
            'result' => $this->logViewer->delete($date) ? 'success' : 'error'
        ]);
    }

}
