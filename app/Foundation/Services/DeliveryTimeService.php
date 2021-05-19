<?php

namespace Foundation\Services;

use Foundation\Models\DeliveryTime;
use Neputer\Supports\BaseService;

/**
 * Class PublisherService
 * @package Foundation\Services
 */
class DeliveryTimeService extends BaseService
{

    /**
     * The Publisher instance
     *
     * @var $model
     */
    protected $model;

    /**
     * PublisherService constructor.
     * @param DeliveryTime $deliveryTime
     */
    public function __construct(DeliveryTime $deliveryTime)
    {
        $this->model = $deliveryTime;
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
            ->latest();
    }

}
