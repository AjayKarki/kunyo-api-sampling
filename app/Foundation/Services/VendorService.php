<?php

namespace Foundation\Services;

use Foundation\Models\Vendor;
use Neputer\Supports\BaseService;

/**
 * Class VendorService
 * @package Foundation\Services
 */
class VendorService extends BaseService
{

    /**
     * The Vendor instance
     *
     * @var $model
     */
    protected $model;

    /**
     * VendorService constructor.
     * @param Vendor $vendor
     */
    public function __construct(Vendor $vendor)
    {
        $this->model = $vendor;
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
        return $this->model->pluck('name', 'id');
    }

}
