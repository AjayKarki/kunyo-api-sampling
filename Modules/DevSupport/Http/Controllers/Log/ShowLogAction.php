<?php

namespace Modules\DevSupport\Http\Controllers\Log;

use Illuminate\Http\Request;
use Arcanedev\LogViewer\Entities\Log;
use Arcanedev\LogViewer\Exceptions\LogNotFoundException;
use Arcanedev\LogViewer\Contracts\LogViewer as LogViewerContract;

/**
 * Class ShowLogAction
 * @package Modules\DevSupport\Http\Controllers
 */
final class ShowLogAction
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

    public function __invoke(Request $request, $date)
    {
        $level   = 'all';
        $log     = $this->getLogOrFail($date);
        $query   = $request->get('query');
        $levels  = $this->logViewer->levelsNames();
        $entries = $log->entries($level)->paginate($this->getLimit());

        return view('support::children.log.show', compact('level', 'log', 'query', 'levels', 'entries'));
    }

    /**
     * Get a log or fail
     *
     * @param  string  $date
     *
     * @return Log|null
     */
    protected function getLogOrFail(string $date)
    {
        $log = null;

        try {
            $log = $this->logViewer->get($date);
        }
        catch (LogNotFoundException $e) {
            abort(404, $e->getMessage());
        }

        return $log;
    }

    private function getLimit()
    {
        return config('support.perPage', $this->perPage);
    }

}
