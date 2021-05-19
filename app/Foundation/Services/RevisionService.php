<?php

namespace Foundation\Services;

use Foundation\Models\Revision;
use Illuminate\Database\Eloquent\Model;

/**
 * Class RevisionService
 * @package Foundation\Services
 */
final class RevisionService
{

    private $model;

    /**
     * RevisionService constructor.
     * @param Revision $revision
     */
    public function __construct(Revision $revision)
    {
        $this->model = $revision;
    }

    /**
     * @param Model $model
     * @param array $data
     * @return mixed
     */
    public function save(Model $model, array $data)
    {
        $revision = $this->model->create($data);
        if ($revision) {
            return $model->revisions()->save($revision);
        }
    }

}
