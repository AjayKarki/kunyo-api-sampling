<?php

namespace Neputer\Supports;

use Neputer\Config\Status;

/**
 * Class BaseService
 * @package Neputer\Supports
 */
abstract class BaseService
{

    /**
     * @param $id
     * @return mixed
     */
    public function findOrFail($id)
    {
        return $this->model->findOrFail($id);
    }

    /**
     * @param array $data
     * @param array $search
     * @return mixed
     */
    public function createOrUpdate( array $data, array $search)
    {
        return $this->model->updateOrCreate(
            $search,
            $data
        );
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function new( array $data )
    {
        return $this->model->create( $data );
    }

    /**
     * @param array $data
     * @param $model
     * @return mixed
     */
    public function update( array $data, $model)
    {
        $instance = $model;
        $model->update( $data );
        return $instance;
    }

    /**
     * @param null $paginate
     * @return mixed
     */
    public function get( $paginate = null )
    {
        $that = $this->model;

        if (is_null($paginate)) {
            return $that->latest()
                ->get();
        }
        return $that->latest()->paginate($paginate);
    }

    /**
     * Here $data is new created record of that model
     * And $tags is single or multiple tag value
     *
     * @param $data
     * @param $tags
     * @return mixed
     */
    public function syncData($data,$tags)
    {
        return $data->tags()->sync((array) $tags);
    }

    /**
     * @param $model
     * @return bool
     */
    public function delete($model)
    {
        $model->delete();
        return true;
    }

    /**
     * Return the instance of the current model
     *
     * @return mixed
     */
    public function query()
    {
        return $this->model->query();
    }

    public function status()
    {
        $all = $this->model
            ->selectRaw('count(*) as total')
            ->selectRaw("count(case when status = '".Status::ACTIVE_STATUS."' then 1 end) as active")
            ->selectRaw("count(case when status = '".Status::INACTIVE_STATUS."' then 1 end) as inactive")
            ->first()
            ->toArray();

        return (array) $all;
    }

    /**
     * Pluck Name and ID of the model.
     *
     * @return mixed
     */
    public function pluck()
    {
        return $this->model->where('status', Status::ACTIVE_STATUS)->pluck('name', 'id');
    }

}
