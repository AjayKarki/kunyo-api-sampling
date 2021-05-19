<?php

namespace App\Http\Controllers\Admin\History;

use Carbon\Carbon;
use Neputer\Supports\BaseController;
use Foundation\Services\HistoryService;

class ListAction extends BaseController
{

    private $historyService;

    public function __construct(HistoryService $historyService)
    {
        $this->historyService = $historyService;
    }

    public function __invoke()
    {
        $historyType = request('history_type');
        $date        = request('date_filter');

        $data['history'] = $this->historyService->query()
            ->when($historyType, function ($query) use($historyType) {
                if ($historyType !== 'all') {
                    $query->where('type', request('history_type'));
                }
            })
            ->when($date, function ($query) use ($date) {
                if ($date !== 'all') {
                    switch ($date) {
                        case 'today':
                            $query->whereDate('created_at', Carbon::today());
                            break;
                        case 'month':
                            $query->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()]);
                            break;
                        case 'yesterday':
                            $query->whereDate('created_at', Carbon::yesterday());
                            break;
                        default:
                            $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                    }
                }
            })
            ->latest()
            ->paginate(25);
        return view('admin.history.index', compact('data'));
    }

}
