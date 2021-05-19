<?php

namespace App\Http\Controllers\Admin;

use Exception;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Foundation\Models\PaymentRegion;
use Neputer\Supports\BaseController;
use Foundation\Requests\PaymentCountry\{
    StoreRequest,
    UpdateRequest
};
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\Factory;
use Foundation\Services\PaymentRegionService;

/**
 * Class PaymentRegionController
 * @package App\Http\Controllers\Admin
 */
class PaymentRegionController extends BaseController
{

    /**
     * The PaymentRegionService instance
     *
     * @var $regionService
     */
    private PaymentRegionService $regionService;

    public function __construct(PaymentRegionService $regionService)
    {
        $this->regionService = $regionService;
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
                ->of($this->regionService->filter($request->input('search.value')))
                ->addColumn('name', function ($data) {
                    return $data->name . ' | <b>' . $data->currency . '</b>';
                })
                ->addColumn('created_at', function ($data) {
                    return $data->created_at . " <code>{$data->created_at->diffForHumans()}</code>";
                })
                ->addColumn('action', function ($data) {
                    $model = 'payment-region';
                    return view('admin.common.data-table-action', compact('data', 'model'))->render();
                })
                ->addColumn('status', function ($data) {
                     return view('admin.payment-region.partials.status', compact('data'))->render();
                })
                ->addColumn('gateways', function ($data) {
                    return view('admin.payment-region.partials.gateways', compact('data'))->render();
                })
                ->rawColumns([ 'name','action', 'created_at', 'gateways', 'status', ])
                ->make(true);
        }

        return view('admin.payment-region.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Factory
     */
    public function create()
    {
        $data = [];
        return view('admin.payment-region.create', compact('data'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  StoreRequest $request
     * @return RedirectResponse
     */
    public function store(StoreRequest $request)
    {
        $this->regionService->new($request->validated());
        flash('success', 'Record successfully created.');
        return $this->redirect($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  PaymentRegion $paymentRegion
     * @return Factory
     */
    public function show(PaymentRegion $paymentRegion)
    {
        $data = [];
        $data['payment-region'] = $paymentRegion;
        return view('admin.payment-region.show', compact('data'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  PaymentRegion $paymentRegion
     * @return Factory
     */
    public function edit(PaymentRegion $paymentRegion)
    {
        $data = [];
        $data['payment-region']  = $paymentRegion;
        return view('admin.payment-region.edit', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UpdateRequest  $request
     * @param  PaymentRegion $paymentRegion
     * @return RedirectResponse
     */
    public function update(UpdateRequest $request, PaymentRegion $paymentRegion)
    {
        $this->regionService->update($request->validated(), $paymentRegion);
        flash('success', 'Record successfully updated.');
        return $this->redirect($request);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  PaymentRegion $paymentRegion
     * @return RedirectResponse
     */
    public function destroy(PaymentRegion $paymentRegion)
    {
        $this->regionService->delete($paymentRegion);
        flash('success', 'PaymentRegion is deleted successfully !');
        return redirect()->back();
    }
}
