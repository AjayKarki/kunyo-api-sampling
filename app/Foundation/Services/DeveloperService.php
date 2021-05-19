<?php

namespace Foundation\Services;

use Foundation\Models\Developer;
use Neputer\Supports\BaseService;

/**
 * Class DeveloperService
 * @package Foundation\Services
 */
class DeveloperService extends BaseService
{

    /**
     * The Developer instance
     *
     * @var $model
     */
    protected $model;

    /**
     * DeveloperService constructor.
     * @param Developer $developer
     */
    public function __construct(Developer $developer)
    {
        $this->model = $developer;
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
            })->latest();
    }

}
