<?php

namespace Foundation\Services;

use Foundation\Models\DeliveryTime;
use Foundation\Models\Region;
use Neputer\Supports\BaseService;

/**
 * Class PublisherService
 * @package Foundation\Services
 */
class RegionService extends BaseService
{

    /**
     * The Publisher instance
     *
     * @var $model
     */
    protected $model;

    /**
     * PublisherService constructor.
     * @param Region $region
     */
    public function __construct(Region $region)
    {
        $this->model = $region;
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
            ->latest()
            ->get();
    }

}
