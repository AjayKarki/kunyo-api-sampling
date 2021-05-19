<?php

namespace Foundation\Services;

use Foundation\Models\Page;
use Neputer\Config\Status;
use Neputer\Supports\BaseService;


/**
 * Class PostService
 * @package Foundation\Services
 */
class PageService extends BaseService
{

    /**
     * The Post instance
     *
     * @var $model
     */
    protected $model;


    /**
     * PageService constructor.
     * @param Page $page
     */
    public function __construct(Page $page)
    {
        $this->model = $page;
    }

    /**
     * Filter
     *
     * @param $type
     * @param array|null $data
     * @return mixed
     */
    public function filter(array $data)
    {
        return $this->model
            ->where(function ($query) use ($data) {
                if ($data['filter']['title']) {
                    $query->where('title', 'like', '%' . $data['filter']['title'] . '%');
                }
                if ($data['filter']['page_type']) {
                    $query->where('page_type', 'like', '%' . $data['filter']['page_type'] . '%');
                }
                if ($data['filter']['status']) {
                    $query->where('status', $data['filter']['status'] == 'active' ? 1 : 0);
                }
            })->orderBy('id', 'DESC')->get();
    }

    public function checkForTitle($title)
    {
        return $this->model->where('title', $title)->count();
    }

}
