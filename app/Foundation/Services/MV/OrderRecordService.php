<?php

namespace Foundation\Services\MV;

use Foundation\Builders\Filters\MV\OrderRecordFilter;
use Neputer\Supports\BaseService;
use Foundation\Models\MySqlView\OrderRecord;

/**
 * Class OrderRecordService
 * @package Foundation\Services\MV
 */
class OrderRecordService extends BaseService
{

    protected $model;

    public function __construct( OrderRecord $orderRecord)
    {
        $this->model = $orderRecord;
    }

    /**
     * Filter
     *
     * @param array $data
     * @return mixed
     */
    public function filter(array $data)
    {
        return OrderRecordFilter::apply(
            $this->model->query(), $data
        );
    }

}
