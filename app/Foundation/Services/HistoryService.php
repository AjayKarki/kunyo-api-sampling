<?php

namespace Foundation\Services;

use Foundation\Models\Genre;
use Foundation\Models\History;
use Illuminate\Database\Eloquent\Model;
use Neputer\Supports\BaseService;

/**
 * Class HistoryService
 * @package Foundation\Services
 */
class HistoryService extends BaseService
{

    /**
     * The Genre instance
     *
     * @var $model
     */
    protected $model;

    /**
     * GenreService constructor.
     * @param History $history
     */
    public function __construct(History $history)
    {
        $this->model = $history;
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

    /**
     * Create a History Entry and Associate With The given Model
     *
     * @param $model
     * @param $data
     * @return mixed
     */
    public function create($model, $data)
    {
        $history = new History($data);
        if ($model)
            $history->historyable()->associate($model);
        return $history->save();
    }

    /**
     * Bulk Insert History
     *
     * @param $data
     * @return mixed
     */
    public function insert($data)
    {
        return $this->model->insert($data);
    }

    /**
     * Get Models based on Conditions
     *
     * @param $conditions
     * @return mixed
     */
    public function getWhere($conditions)
    {
        return $this->model->where($conditions)->latest()->limit(10)->get();
    }

}
