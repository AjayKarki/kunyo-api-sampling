<?php

namespace Foundation\Services;

use Foundation\Models\Pricing;
use Neputer\Supports\BaseService;

/**
 * Class PricingService
 * @package Foundation\Services
 */
class PricingService extends BaseService
{

    /**
     * The Pricing instance
     *
     * @var $model
     */
    protected $model;

    /**
     * PricingService constructor.
     * @param Pricing $pricing
     */
    public function __construct(Pricing $pricing)
    {
        $this->model = $pricing;
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

}
