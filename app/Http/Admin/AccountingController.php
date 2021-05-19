<?php

namespace App\Http\Controllers\Admin;

use Exception;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Foundation\Models\Accounting;
use Neputer\Supports\BaseController;
use Foundation\Requests\Accounting\{
    StoreRequest,
    UpdateRequest
};
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\Factory;
use Foundation\Services\AccountingService;

/**
 * Class AccountingController
 * @package App\Http\Controllers\Admin
 */
class AccountingController extends BaseController
{

    /**
     * The AccountingService instance
     *
     * @var $accountingService
     */
    private $accountingService;

    public function __construct(AccountingService $accountingService)
    {
        $this->accountingService = $accountingService;
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
                ->of($this->accountingService->filter($request->only('search.value', 'filter')))
                ->addColumn('order', function ($data) {
                    return '<a href="' . route('admin.order.view.action', $data->id) . '"> #' . $data->transaction->transaction_id . '</a>';
                })
                ->addColumn('quantity', function ($data) {
                    return $data->quantity;
                })
                ->addColumn('discount', function ($data) {
                    $discount = (is_null($data->discount) || empty($data->discount)) ? '0' : $data->discount;
                    return 'Rs. ' . $data->discounted_amount . ' (' . $discount . ')';
                })
                ->addColumn('original_price', function ($data) {
                    // Let the selling price be the original price if original price is 0
                    $originalPrice = $data->original_price == 0 ? $data->selling_price : $data->original_price;
                    return 'Rs. ' . $originalPrice * $data->quantity . '<br><code>Rs. ' . $originalPrice . '/unit</code>';
                })
                ->addColumn('selling_price', function ($data) {
                    return 'Rs. ' . $data->selling_price * $data->quantity . '<br><code>Rs. ' . $data->selling_price . '/unit</code>';
                })
                ->addcolumn('service_charge', function ($data){
                    return 'Rs. ' . ($data->transaction->service_charge ?? 0);
                })
                ->addColumn('profit_loss', function ($data) {
                    // Let the selling price be the original price if original price is 0
                    $originalPrice = $data->original_price == 0 ? $data->selling_price : $data->original_price;
                    if($originalPrice != 0){
                        $amount = $data->quantity * ( $data->selling_price - $originalPrice) - $data->discounted_amount;
                        return view('admin.accounting.partials.profit-loss', compact('amount'))->render();
                    }else
                        return '<code>--</code>';
                })
                ->rawColumns([ 'order', 'profit_loss', 'original_price', 'selling_price', ])
                ->make(true);
        }
        return view('admin.accounting.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Factory
     */
    public function create()
    {
        $data = [];
        return view('admin.accounting.create', compact('data'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  StoreRequest $request
     * @return RedirectResponse
     */
    public function store(StoreRequest $request)
    {
        $this->accountingService->new($request->all());
        flash('success', 'Record successfully created.');
        return $this->redirect($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  Accounting $accounting
     * @return Factory
     */
    public function show(Accounting $accounting)
    {
        $data = [];
        $data['accounting'] = $accounting;
        return view('admin.accounting.show', compact('data'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Accounting $accounting
     * @return Factory
     */
    public function edit(Accounting $accounting)
    {
        $data = [];
        $data['accounting']  = $accounting;
        return view('admin.accounting.edit', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UpdateRequest  $request
     * @param  Accounting $accounting
     * @return RedirectResponse
     */
    public function update(UpdateRequest $request, Accounting $accounting)
    {
        $this->accountingService->update($request->all(), $accounting);
        flash('success', 'Record successfully updated.');
        return $this->redirect($request);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Accounting $accounting
     * @return RedirectResponse
     */
    public function destroy(Accounting $accounting)
    {
        $this->accountingService->delete($accounting);
        flash('success', 'Accounting is deleted successfully !');
        return redirect()->back();
    }
}
