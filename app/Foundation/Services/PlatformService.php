<?php

namespace Foundation\Services;

use Foundation\Models\Platform;
use Neputer\Supports\BaseService;

/**
 * Class PlatformService
 * @package Foundation\Services
 */
class PlatformService extends BaseService
{

    /**
     * The Platform instance
     *
     * @var $model
     */
    protected $model;

    /**
     * PlatformService constructor.
     * @param Platform $platform
     */
    public function __construct(Platform $platform)
    {
        $this->model = $platform;
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
