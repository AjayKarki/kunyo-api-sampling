<?php

namespace Foundation\Services;

use Foundation\Models\AccountingCategory;
use Neputer\Supports\BaseService;

/**
 * Class AccountingCategoryService
 * @package Foundation\Services
 */
class AccountingCategoryService extends BaseService
{

    /**
     * The AccountingCategory instance
     *
     * @var $model
     */
    protected $model;

    /**
     * AccountingCategoryService constructor.
     * @param AccountingCategory $accountingCategory
     */
    public function __construct(AccountingCategory $accountingCategory)
    {
        $this->model = $accountingCategory;
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

    public function pluckByType($type)
    {
        return $this->model->whereJsonContains('type', "{$type}")->pluck('name', 'id');
    }

}
