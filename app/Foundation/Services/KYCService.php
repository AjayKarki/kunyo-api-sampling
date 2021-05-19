<?php

namespace Foundation\Services;

use Foundation\Lib\KYC as KYCLib;
use Foundation\Models\KYC;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Neputer\Supports\BaseService;

/**
 * Class KYCService
 * @package Foundation\Services
 */
class KYCService extends BaseService
{

    /**
     * The KYC instance
     *
     * @var $model
     */
    protected $model;

    /**
     * KYCService constructor.
     * @param KYC $kYC
     */
    public function __construct(KYC $kYC)
    {
        $this->model = $kYC;
    }

    /**
     * Filter
     *
     * @param array $data
     * @return mixed
     */
    public function filter(array $data)
    {
        $search = Arr::get($data, 'search.value');
        $status = Arr::get($data, 'filter.status');

        return $this->model
            ->where(function ($query) use ($search, $status){
                if($search){
                    $query->whereHas('user', function ($query) use ($search){
                        $query->where(DB::raw('CONCAT_WS(" ", first_name, last_name)'), 'like', '%'. $search . '%');
                    });
                    $query->orWhere(DB::raw('CONCAT_WS(" ", first_name, last_name)'), 'like', '%'. $search . '%');
                    $query->orWhere('email', 'like', '%'. $search . '%');
                    $query->orWhere('phone', 'like', '%'. $search . '%');

                }
                if ($status != 0)
                    $query->where('verification_status', $status);
            })
            ->with('user:id,first_name,middle_name,last_name', 'user.roles');
    }


    /**
     * Get Count of Requests by Verification status
     *
     * @param Request $request
     * @return mixed
     */
    public function getCountByStatus(Request $request)
    {
        $startDate = $this->getStartDate($request->get('type'));

        $query =$this->model
            ->selectRaw("count(case when verification_status = '". KYCLib::STATUS_SUBMITTED ."' then 1 end) as pending")
            ->selectRaw("count(case when verification_status = '". KYCLib::STATUS_VERIFIED ."' then 1 end) as verified")
            ->selectRaw("count(case when verification_status = '". KYCLib::STATUS_REJECTED ."' then 1 end) as rejected")
            ->selectRaw('count(*) as total');

        if($startDate){
            $query->whereBetween('created_at', [ $startDate, now(), ]);
        }

        return $query->first()->toArray();
    }

    /**
     * Get number of Pending Requests
     *
     * @return mixed
     */
    public function countPendingRequests()
    {
        return $this->model->where('verification_status', KYCLib::STATUS_SUBMITTED)->count();
    }


    /**
     * Get The starting created at date
     *
     * @param $type
     * @return Carbon|null
     */
    private function getStartDate($type)
    {
        $startDate = null;
        if( $type ){
            switch ($type){
                case 2:
                    $startDate = now()->startOfDay();
                    break;
                case 3:
                    $startDate = now()->startOfWeek();
                    break;
                case 4:
                    $startDate = now()->startOfMonth();
                    break;
                case 5:
                    $startDate = now()->startOfYear();
                    break;
                default:
                    $startDate = null;
            }
        }

        return $startDate;
    }
}
