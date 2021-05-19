<?php

namespace Foundation\Services;

use Foundation\Models\DeliveryMode;
use Neputer\Supports\BaseService;

/**
 * Class PublisherService
 * @package Foundation\Services
 */
class DeliveryModeService extends BaseService
{

    /**
     * The Publisher instance
     *
     * @var $model
     */
    protected $model;

    /**
     * PublisherService constructor.
     * @param DeliveryMode $deliveryMode
     */
    public function __construct(DeliveryMode $deliveryMode)
    {
        $this->model = $deliveryMode;
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
