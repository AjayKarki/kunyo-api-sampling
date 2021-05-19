<?php

namespace Foundation\Services;

use Foundation\Models\Publisher;
use Neputer\Supports\BaseService;

/**
 * Class PublisherService
 * @package Foundation\Services
 */
class PublisherService extends BaseService
{

    /**
     * The Publisher instance
     *
     * @var $model
     */
    protected $model;

    /**
     * PublisherService constructor.
     * @param Publisher $publisher
     */
    public function __construct(Publisher $publisher)
    {
        $this->model = $publisher;
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
