<?php

namespace App\Http\Controllers\Admin;

use Exception;
use Foundation\Events\DiscountVoucherCreated;
use Foundation\Lib\Role;
use Foundation\Services\UserService;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Foundation\Models\DiscountVoucher;
use Neputer\Supports\BaseController;
use Foundation\Requests\DiscountVoucher\{
    StoreRequest,
    UpdateRequest
};
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\Factory;
use Foundation\Services\DiscountVoucherService;
use App\Foundation\Lib\DiscountVoucher as DiscountVoucherLib;

/**
 * Class DiscountVoucherController
 * @package App\Http\Controllers\Admin
 */
class DiscountVoucherController extends BaseController
{

    /**
     * The DiscountVoucherService instance
     *
     * @var $discountVoucherService
     */
    private $discountVoucherService;
    /**
     * @var UserService
     */
    private $userService;

    /**
     * DiscountVoucherController constructor.
     * @param DiscountVoucherService $discountVoucherService
     * @param UserService $userService
     */
    public function __construct(DiscountVoucherService $discountVoucherService, UserService $userService)
    {
        $this->discountVoucherService = $discountVoucherService;
        $this->userService = $userService;
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
                ->of($this->discountVoucherService->filter($request->only('search.value', 'filter')))
                ->addColumn('name', function ($data) {
                    return $data->name;
                })
                ->addColumn('type', function ($data) {
                    return view('admin.discount-voucher.partials.use-type', compact('data'))->render();
                })
                ->addColumn('voucher', function ($data) {
                    return $data->voucher;
                })
                ->addColumn('duration', function ($data) {
                    return view('admin.discount-voucher.partials.duration', compact('data'))->render();
                })
                ->addColumn('amount', function ($data) {
                    if ($data->type == DiscountVoucherLib::TYPE_PERCENT)
                        $amount = $data->discount_percent . ' %';
                    elseif ($data->type == DiscountVoucherLib::TYPE_AMOUNT)
                        $amount = 'Rs. ' . $data->discount_amount;
                    return $amount ?? '-';
                })
                ->addColumn('usage', function ($data) {
                    return view('admin.discount-voucher.partials.usage  ', compact('data'))->render();
                })
                ->addColumn('min_order_amount', function ($data) {
                    return 'Rs. ' . $data->min_order_amount ?? 0;
                })
                ->addColumn('status', function ($data) {
                     return view('admin.common.status', compact('data'))->render();
                })
                ->addColumn('action', function ($data) {
                    $model = 'discount-voucher';
                    return view('admin.common.data-table-action', compact('data', 'model'))->render();
                })
                ->rawColumns(['duration', 'action', 'usage', 'status', 'type', ])
                ->make(true);
        }
        $data = [];
        $data['count'] = $this->discountVoucherService->getCount($request);
        return view('admin.discount-voucher.index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Factory
     */
    public function create()
    {
        $data = [];
        $data['customers'] = [];
        return view('admin.discount-voucher.create', compact('data'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  StoreRequest $request
     * @return RedirectResponse
     */
    public function store(StoreRequest $request)
    {
        if($request->get('use_type') == DiscountVoucherLib::USE_TYPE_GLOBAL)
            $request->request->remove('user_id');

        $voucher = $this->discountVoucherService->new(array_filter($request->all()));
        flash('success', 'Voucher successfully created.');

        if($request->get('use_type') == DiscountVoucherLib::USE_TYPE_SINGLE_USER)
            DiscountVoucherCreated::dispatch($voucher);

        return redirect()->route('admin.discount-voucher.show', $voucher->id);
    }

    /**
     * Display the specified resource.
     *
     * @param  DiscountVoucher $discountVoucher
     * @return Factory
     */
    public function show(DiscountVoucher $discountVoucher)
    {
        $data = [];
        $data['discount-voucher'] = $discountVoucher->load('customer:id,first_name,middle_name,last_name');
        return view('admin.discount-voucher.show', compact('data'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  DiscountVoucher $discountVoucher
     * @return Factory
     */
    public function edit(DiscountVoucher $discountVoucher)
    {
        $data = [];
        $data['discount-voucher']  = $discountVoucher;
        // $data['customers'] = $this->userService->pluckUserWithEmailByRole('customer');
        $data['customers'] = $this->userService->pluckDiscountVoucherCustomer($discountVoucher->user_id);

        return view('admin.discount-voucher.edit', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UpdateRequest  $request
     * @param  DiscountVoucher $discountVoucher
     * @return RedirectResponse
     */
    public function update(UpdateRequest $request, DiscountVoucher $discountVoucher)
    {
        if($request->get('use_type') == DiscountVoucherLib::USE_TYPE_GLOBAL)
            $request->request->remove('user_id');
        $this->discountVoucherService->update($request->all(), $discountVoucher);
        flash('success', 'Record successfully updated.');
        return $this->redirect($request);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  DiscountVoucher $discountVoucher
     * @return RedirectResponse
     */
    public function destroy(DiscountVoucher $discountVoucher)
    {
        $this->discountVoucherService->delete($discountVoucher);
        flash('success', 'DiscountVoucher is deleted successfully !');
        return redirect()->back();
    }
}
