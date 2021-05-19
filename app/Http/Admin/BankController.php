<?php

namespace App\Http\Controllers\Admin;

use Exception;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Foundation\Models\Bank;
use Neputer\Supports\BaseController;
use Foundation\Requests\Bank\{
    StoreRequest,
    UpdateRequest
};
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\Factory;
use Foundation\Services\BankService;

/**
 * Class BankController
 * @package App\Http\Controllers\Admin
 */
class BankController extends BaseController
{

    /**
     * The BankService instance
     *
     * @var $bankService
     */
    private $bankService;

    public function __construct(BankService $bankService)
    {
        $this->bankService = $bankService;
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
                ->of($this->bankService->filter($request->input('search.value')))
                ->addColumn('created_at', function ($data) {
                    return $data->created_at . " <code>{$data->created_at->diffForHumans()}</code>";
                })
                ->addColumn('action', function ($data) {
                    $model = 'bank';
                    return view('admin.common.data-table-action', compact('data', 'model'))->render();
                })
                ->addColumn('status', function ($data) {
                    return view('admin.common.status', compact('data'))->render();
                })
                ->addColumn('checkbox', function ($data) {
                    return view('admin.common.checkbox', compact('data'))->render();
                })
                ->rawColumns([ 'checkbox', 'action', 'created_at', 'status', ])
                ->make(true);
        }

        return view('admin.bank.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Factory
     */
    public function create()
    {
        $data = [];
        //
        return view('admin.bank.create', compact('data'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  StoreRequest $request
     * @return RedirectResponse
     */
    public function store(StoreRequest $request)
    {
        $this->bankService->new($request->all());
        flash('success', 'Record successfully created.');
        return $this->redirect($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  Bank $bank
     * @return Factory
     */
    public function show(Bank $bank)
    {
        $data = [];
        $data['bank'] = $bank;
        return view('admin.bank.show', compact('data'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Bank $bank
     * @return Factory
     */
    public function edit(Bank $bank)
    {
        $data = [];
        $data['bank']  = $bank;
        return view('admin.bank.edit', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UpdateRequest  $request
     * @param  Bank $bank
     * @return RedirectResponse
     */
    public function update(UpdateRequest $request, Bank $bank)
    {
        $this->bankService->update($request->all(), $bank);
        flash('success', 'Record successfully updated.');
        return $this->redirect($request);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Bank $bank
     * @return RedirectResponse
     */
    public function destroy(Bank $bank)
    {
        $this->bankService->delete($bank);
        flash('success', 'Bank is deleted successfully !');
        return redirect()->back();
    }
}
