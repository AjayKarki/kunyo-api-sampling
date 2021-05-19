<?php


namespace App\Http\Controllers\Admin\Actions;


use App\Foundation\Lib\History;
use Foundation\Models\User;
use Foundation\Services\HistoryService;
use Illuminate\Http\Request;

class HistoryAction
{
    /**
     * Get History for Item
     *
     * @param Request $request
     * @return array|string
     * @throws \Throwable
     */
    public function show(Request $request)
    {
        $data = app(HistoryService::class)->getWhere([
            'historyable_id'   => $request->get('id'),
            'historyable_type' => $request->get('type'),
            'type'             => History::TYPE_USER_ROLE_UPDATED,
        ]);

        $type = $request->get('type') == User::class ? 'role' : 'value';
        return view('admin.history.show', compact('data', 'type'))->render();
    }

}
