<?php

namespace App\Http\Controllers\Admin\Actions;

use Carbon\Carbon;
use Foundation\Lib\Date;
use Foundation\Models\User;
use Illuminate\Database\Query\Builder;
use Schema;
use Neputer\Config\Status;
use Illuminate\Http\Request;

/**
 * Class CountAction
 * @package App\Http\Controllers\Admin\Actions
 */
class CountAction
{

    /**
     * Return the count of models by status
     *
     * @param Request $request
     * @param $table
     * @return mixed
     */
    public function index(Request $request, $table)
    {
        if (Schema::hasTable($table)){
            $query = app('db')
                ->table($table)
                ->selectRaw('count(*) as total')
                ->selectRaw("count(case when status = '".Status::ACTIVE_STATUS."' then 1 end) as active")
                ->selectRaw("count(case when status = '".Status::INACTIVE_STATUS."' then 1 end) as inactive");

            $query =  $this->resolveDateRange($request, $query);

            $all = $query->first();
        } else {
            return response()->json(['msg' => 'Error Getting Data'], 503);
        }

        $data['status'] = (array) $all;

        return $data;
    }

    /**
     * Get Count of Users by status
     *
     * @param Request $request
     * @return mixed
     */
    public function user(Request $request)
    {
        $startDate = $this->getStartDate($request);
        $query = User::selectRaw('count(*) as total')
            ->selectRaw("count(case when status = '".Status::ACTIVE_STATUS."' then 1 end) as active")
            ->selectRaw("count(case when status = '".Status::INACTIVE_STATUS."' then 1 end) as inactive");

        if($startDate){
            $query->whereBetween('created_at', [ $startDate, now(), ]);
        }

        if($role = $request->get('role')){
            $query->whereHas('roles', function ($query) use ($role){
                $query->where('slug', $role);
            });
        }
        $data['status'] = $query->first()->toArray();
        return $data;
    }

    private function resolveDateRange(Request $request, Builder $query): Builder
    {
        if( $type = $request->get('type') ){
            switch ($type){
                case Date::DATE_TODAY:
                    $query = $query->whereDate('created_at', today());
                    break;
                case Date::DATE_YESTERDAY:
                    $query = $query->whereDate('created_at', Carbon::yesterday());
                    break;
                case Date::DATE_WEEK:
                    $query = $query->whereBetween('created_at', [ now()->startOfWeek(), now()->endOfWeek() ]);
                    break;
                case Date::DATE_MONTH:
                    $query =  $query->whereDate('created_at', now()->startOfMonth());
                    break;
                case Date::DATE_YEAR:
                    $query = $query->whereDate('created_at', now()->startOfYear());
                    break;
            }
        }

        return $query;
    }
}
