<?php

namespace Foundation\Services;

use Foundation\Lib\AccountingCategory;
use Foundation\Lib\ExpenseCategory;
use Foundation\Models\Expense;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Neputer\Supports\BaseService;

/**
 * Class ExpenseService
 * @package Foundation\Services
 */
class ExpenseService extends BaseService
{

    /**
     * The Expense instance
     *
     * @var $model
     */
    protected $model;

    /**
     * ExpenseService constructor.
     * @param Expense $expense
     */
    public function __construct(Expense $expense)
    {
        $this->model = $expense;
    }

    /**
     * Filter
     *
     * @param array $data
     * @return mixed
     */
    public function filter(array $data)
    {
        $query = $this->model->newQuery();

        if ($searchKey = Arr::get($data, 'search.value')) {
            $query->where('title', 'like', '%' . $searchKey . '%');
        }

        if ($startDate = Arr::get($data, 'filter.startDate')) {
            $query->where('created_at', '>=', $startDate);
        }

        if ($endDate = Arr::get($data, 'filter.endDate')) {
            $query->where('created_at', '<=', $endDate);
        }

        return $query->with('category:id,name');
    }

    /**
     * Get the Accounting statistics
     *
     * @param array $filter
     * @return mixed
     */
    public function getStats(array $filter = [])
    {
        $startDate = $filter['startDate'] ?? null;
        $endDate = $filter['endDate'] ?? null;
        $dailyDate = $filter['dailyDate'] ?? null;

        $query = $this->model->query();

        if($dailyDate)
            $query->whereDate('created_at', $dailyDate);
        else{
            if($startDate)
                $query->whereDate('created_at', '>=', $startDate);
            if($endDate)
                $query->whereDate('created_at', '<=', $endDate);
        }

        $expense = $query->get();

        $data = [];
        $incomeAmt = $expense->where('type', AccountingCategory::TYPE_INCOME)->sum('amount');
        $expenseAmt = $expense->where('type', AccountingCategory::TYPE_EXPENSE)->sum('amount');

        $data['income_amount'] = nrp($incomeAmt);
        $data['expense_amount'] = nrp($expenseAmt);
        $data['net_amount'] = nrp($incomeAmt - $expenseAmt);

        $data['net'] = $incomeAmt - $expenseAmt;

        $categories = app(AccountingCategoryService::class)->get();

        $data['income'] = [];
        $data['expense'] = [];
        foreach ($categories as $category){
            if(in_array( strval(AccountingCategory::TYPE_INCOME), $category->type ))
                $data['income'][$category->name] = round($expense->where('category_id', $category->id)->where('type', AccountingCategory::TYPE_INCOME)->sum('amount'), 2);
            if(in_array( strval(AccountingCategory::TYPE_EXPENSE), $category->type ))
                $data['expense'][$category->name] = round($expense->where('category', $category)->where('type', AccountingCategory::TYPE_EXPENSE)->sum('amount'), 2);
        }
        return $data;
    }

}
