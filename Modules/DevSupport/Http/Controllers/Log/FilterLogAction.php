<?php

namespace Modules\DevSupport\Http\Controllers\Log;

use Arcanedev\LogViewer\Entities\LogEntry;
use Arcanedev\LogViewer\Entities\LogEntryCollection;
use Illuminate\Http\Request;
use Arcanedev\LogViewer\Entities\Log;
use Arcanedev\LogViewer\Exceptions\LogNotFoundException;
use Arcanedev\LogViewer\Contracts\LogViewer as LogViewerContract;
use Illuminate\Support\Str;

/**
 * Class FilterLogAction
 * @package Modules\DevSupport\Http\Controllers\Log
 */
final class FilterLogAction
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

    public function __invoke(Request $request, $date, $level = 'all')
    {
        $query   = $request->get('query');

        if (is_null($query)) {
            return redirect()->route('support::support.log.show', [$date]);
        }

        $log     = $this->getLogOrFail($date);
        $levels  = $this->logViewer->levelsNames();

        $needles = array_map(function ($needle) {
            return Str::lower($needle);
        }, array_filter(explode(' ', $query)));

        $entries = $log->entries($level)
            ->unless(empty($needles), function (LogEntryCollection $entries) use ($needles) {
                return $entries->filter(function (LogEntry $entry) use ($needles) {
                    return Str::containsAll(Str::lower($entry->header), $needles);
                });
            })
            ->paginate($this->perPage);

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
