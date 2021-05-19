<?php

namespace App\Http\Controllers\Admin;

use Exception;
use Foundation\Lib\AccountingCategory;
use Foundation\Lib\Category;
use Foundation\Lib\ExpenseCategory;
use Foundation\Services\AccountingCategoryService;
use Foundation\Services\ImageService;
use Foundation\Services\SettingService;
use Foundation\Services\VendorService;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Foundation\Models\Expense;
use Neputer\Supports\BaseController;
use Foundation\Requests\Expense\{
    StoreRequest,
    UpdateRequest
};
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\Factory;
use Foundation\Services\ExpenseService;

/**
 * Class ExpenseController
 * @package App\Http\Controllers\Admin
 */
class ExpenseController extends BaseController
{

    /**
     * The ExpenseService instance
     *
     * @var $expenseService
     */
    private $expenseService;
    /**
     * @var ImageService
     */
    private $imageService;
    /**
     * @var AccountingCategoryService
     */
    private $categoryService;
    /**
     * @var VendorService
     */
    private $vendorService;

    public function __construct(ExpenseService $expenseService, ImageService $imageService, AccountingCategoryService $categoryService, VendorService $vendorService)
    {
        $this->expenseService = $expenseService;
        $this->imageService = $imageService;
        $this->categoryService = $categoryService;
        $this->vendorService = $vendorService;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return Factory|View
     * @throws Exception
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return datatables()
                ->of($this->expenseService->filter($request->only('search.value', 'filter')))
                ->addColumn('title', function ($data) {
                    $type = $data->type == 1 ? '<span class="badge badge-success">income</span>' : '<span class="badge badge-danger">expense</span>';
                    return $data->title . '<br>' . $type;
                })
                ->addColumn('category', function ($data) {
                    return $data->category->name;
                })
                ->addColumn('amount', function ($data) {
                    return 'Rs. ' . $data->amount . "<br> <code>By {$data->payment_method}</code>";
                })
                ->addColumn('payee', function ($data) {
                    return $data->payee ?? '<code> None </code>';
                })
                ->addColumn('transaction_date', function ($data) {
                    return $data->transaction_date . " <code>{$data->transaction_date->diffForHumans()}</code>";
                })
                ->addColumn('action', function ($data) {
                    $model = 'expense';
                    return view('admin.common.data-table-action', compact('data', 'model'))->render();
                })
                ->rawColumns([ 'title', 'payee', 'amount', 'action', 'transaction_date', ])
                ->make(true);
        }

        return view('admin.expense.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Factory
     */
    public function create()
    {
        $data = [];
        $data['categories'] = $this->categoryService->pluckByType(AccountingCategory::TYPE_EXPENSE);
        $data['vendor'] = $this->vendorService->get();
        $data['gateways'] = app(SettingService::class)->pluck('expense_payment_gateways')[0] ?? [];
        return view('admin.expense.create', compact('data'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  StoreRequest $request
     * @return RedirectResponse
     */
    public function store(StoreRequest $request)
    {
        $expense = $this->expenseService->new($request->except('receipt'));
        if($request->hasFile('receipt')){
            $this->imageService->insert([$request->file('receipt')], $expense, 'expenses');
        }
        flash('success', 'Expense Added');
        return redirect()->route('admin.expense.show', $expense->id);
    }

    /**
     * Display the specified resource.
     *
     * @param  Expense $expense
     * @return Factory
     */
    public function show(Expense $expense)
    {
        $data = [];
        $data['expense'] = $expense->load('images', 'category:id,name');
        return view('admin.expense.show', compact('data'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Expense $expense
     * @return Factory
     */
    public function edit(Expense $expense)
    {
        $data = [];
        $data['expense']  = $expense->load('images');
        $data['categories'] = $this->categoryService->pluckByType(AccountingCategory::TYPE_EXPENSE);
        $data['vendor'] = $this->vendorService->get();
        $data['gateways'] = app(SettingService::class)->pluck('expense_payment_gateways')[0] ?? [];
        return view('admin.expense.edit', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UpdateRequest  $request
     * @param  Expense $expense
     * @return RedirectResponse
     */
    public function update(UpdateRequest $request, Expense $expense)
    {
        if($request->hasFile('receipt')){
            if ($expense->images){
                $this->imageService->remove([$expense->images->path]);
            }
            $this->imageService->insert([$request->file('receipt')], $expense, 'expenses');
        }
        $this->expenseService->update($request->except('receipt'), $expense);
        flash('success', 'Record successfully updated.');
        return $this->redirect($request);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Expense $expense
     * @return RedirectResponse
     */
    public function destroy(Expense $expense)
    {
        $this->expenseService->delete($expense);
        flash('success', 'Expense is deleted successfully !');
        return redirect()->back();
    }
}
