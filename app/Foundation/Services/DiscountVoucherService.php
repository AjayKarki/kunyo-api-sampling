<?php

namespace Foundation\Services;

use Carbon\Carbon;
use Foundation\Models\DiscountVoucher;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Neputer\Supports\BaseService;

/**
 * Class DiscountVoucherService
 * @package Foundation\Services
 */
class DiscountVoucherService extends BaseService
{

    /**
     * The DiscountVoucher instance
     *
     * @var $model
     */
    protected $model;

    /**
     * DiscountVoucherService constructor.
     * @param DiscountVoucher $discountVoucher
     */
    public function __construct(DiscountVoucher $discountVoucher)
    {
        $this->model = $discountVoucher;
    }

    /**
     * Filter
     *
     * @param array $data
     * @return mixed
     */
    public function filter(array $data)
    {
        $query = $this->model->newQuery();
        if ($searchKey = Arr::get($data, 'search.value')) {
            $query = $this->filterSearch($query, $searchKey);
        }

        if ($startS = Arr::get($data, 'filter.start_date.start')) {
            $query->whereDate('start_date', '>=', $startS);
        }
        if ($endS = Arr::get($data, 'filter.start_date.end')) {
            $query->where('start_date', '<=', $endS);
        }

        if ($startE = Arr::get($data, 'filter.start_date.start')) {
            $query->where('end_date', '>=', $startE);
        }
        if ($endE = Arr::get($data, 'filter.start_date.end')) {
            $query->whereDate('end_date', '<=', $endE);
        }

        if (Arr::get($data, 'filter.status') !== null) {
            if(Arr::get($data, 'filter.status') == 0){
                $query->where('end_date', '<=', Carbon::now()); // Expired
            }
            else
                $query->where('end_date', '>=', Carbon::now());  // Active
        }
        $query->with('customer:id,first_name,middle_name,last_name');

        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Search Box
     *
     * @param $query
     * @param $search
     * @return mixed
     */
    private function filterSearch($query, $search)
    {
        return $query->where(function ($query) use ($search){
            $query->where('name', 'like', '%' . $search . '%')
                ->orWhere('voucher' , 'like', '%' . $search . '%');
        });
    }

    public function getCount(Request $request)
    {
        $query = $this->model->newQuery();
        if($startS = $request->get('start_date.start')){
            $query->whereDate('start_date', '>=', $startS);
        }
        if ($endS = $request->get('start_date.end')) {
            $query->where('start_date', '<=', $endS);
        }

        if($startS = $request->get('end_date.start')){
            $query->whereDate('end_date', '>=', $startS);
        }
        if ($endS = $request->get('end_date.end')) {
            $query->where('end_date', '<=', $endS);
        }
        return  $query->selectRaw("count(case when end_date >= '" . Carbon::now() . "' then 1 end) as active")
            ->selectRaw("count(case when end_date < '" . Carbon::now() . "' then 1 end) as inactive")
            ->first();
    }

}
