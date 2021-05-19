<?php

namespace Foundation\Services;

use Foundation\Models\PaymentRegion;
use Neputer\Supports\BaseService;

/**
 * Class PaymentRegionService
 * @package Foundation\Services
 */
class PaymentRegionService extends BaseService
{

    /**
     * The PaymentRegion instance
     *
     * @var $model
     */
    protected $model;

    /**
     * PaymentRegionService constructor.
     * @param PaymentRegion $paymentCountry
     */
    public function __construct(PaymentRegion $paymentCountry)
    {
        $this->model = $paymentCountry;
    }

    /**
     * Filter
     *
     * @param string|null $name
     * @return mixed
     */
    public function filter(string $name = null)
    {
        return $this->model
            ->where(function ($query) use ($name){
                if($name){
                    $query->where('name','like', '%'. $name .'%');
                }
            })
            ->get();
    }

    public function pluck()
    {
        return $this->model
//            ->selectRaw('CONCAT(name, " | ", currency) as region, id')
            ->selectRaw('name as region, id')
            ->pluck('region', 'id')
            ->toArray();
    }

}
