<?php

namespace App\Http\Controllers\Admin;

use Exception;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Foundation\Models\AccountingCategory;
use Neputer\Supports\BaseController;
use Foundation\Requests\AccountingCategory\{
    StoreRequest,
    UpdateRequest
};
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\Factory;
use Foundation\Services\AccountingCategoryService;

/**
 * Class AccountingCategoryController
 * @package App\Http\Controllers\Admin
 */
class AccountingCategoryController extends BaseController
{

    /**
     * The AccountingCategoryService instance
     *
     * @var $accountingCategoryService
     */
    private $accountingCategoryService;

    public function __construct(AccountingCategoryService $accountingCategoryService)
    {
        $this->accountingCategoryService = $accountingCategoryService;
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
                ->of($this->accountingCategoryService->filter($request->input('search.value')))
                ->addColumn('created_at', function ($data) {
                    return $data->created_at . " <code>{$data->created_at->diffForHumans()}</code>";
                })
                ->addColumn('action', function ($data) {
                    $model = 'accounting-category';
                    return view('admin.common.data-table-action', compact('data', 'model'))->render();
                })
                ->addColumn('status', function ($data) {
                     return view('admin.common.status', compact('data'))->render();
                })
                ->addColumn('type', function ($data){
                    return view('admin.accounting-category.partials.type', compact('data'))->render();
                })
                ->addColumn('slug', function ($data){
                    return "<code> {$data->slug}</code>";
                })
                ->rawColumns([ 'action', 'created_at', 'status', 'type', 'slug', ])
                ->make(true);
        }

        return view('admin.accounting-category.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Factory
     */
    public function create()
    {
        $data = [];
        return view('admin.accounting-category.create', compact('data'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  StoreRequest $request
     * @return RedirectResponse
     */
    public function store(StoreRequest $request)
    {
        $this->accountingCategoryService->new($request->all());
        flash('success', 'Category Created');
        return $this->redirect($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  AccountingCategory $accountingCategory
     * @return Factory
     */
    public function show(AccountingCategory $accountingCategory)
    {
        $data = [];
        $data['accounting-category'] = $accountingCategory;
        return view('admin.accounting-category.show', compact('data'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  AccountingCategory $accountingCategory
     * @return Factory
     */
    public function edit(AccountingCategory $accountingCategory)
    {
        $data = [];
        $data['accounting-category']  = $accountingCategory;
        return view('admin.accounting-category.edit', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UpdateRequest  $request
     * @param  AccountingCategory $accountingCategory
     * @return RedirectResponse
     */
    public function update(UpdateRequest $request, AccountingCategory $accountingCategory)
    {
        $this->accountingCategoryService->update($request->all(), $accountingCategory);
        flash('success', 'Record successfully updated.');
        return $this->redirect($request);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  AccountingCategory $accountingCategory
     * @return RedirectResponse
     */
    public function destroy(AccountingCategory $accountingCategory)
    {
        $this->accountingCategoryService->delete($accountingCategory);
        flash('success', 'AccountingCategory is deleted successfully !');
        return redirect()->back();
    }
}
