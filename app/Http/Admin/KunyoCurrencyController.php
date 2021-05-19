<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use Exception;
use Foundation\Services\UserService;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Foundation\Models\KunyoCurrency;
use Neputer\Supports\BaseController;
use Foundation\Requests\KunyoCurrency\{
    StoreRequest,
    UpdateRequest
};
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\Factory;
use Foundation\Services\KunyoCurrencyService;

/**
 * Class KunyoCurrencyController
 * @package App\Http\Controllers\Admin
 */
class KunyoCurrencyController extends BaseController
{

    /**
     * The KunyoCurrencyService instance
     *
     * @var $kunyoCurrencyService
     */
    private $kunyoCurrencyService;

    public function __construct(KunyoCurrencyService $kunyoCurrencyService)
    {
        $this->kunyoCurrencyService = $kunyoCurrencyService;
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
                ->of($this->kunyoCurrencyService->filter($request->only('search.value', 'filter')))
                ->addColumn('order', function ($data) {
                    return '# ' . $data->order_id;
                })
                ->addColumn('buyer', function ($data) {
                    return "<a href='" . route('admin.user.show', $data->user_id) . "' target='_blank'>{$data->user_name}</a>";
                })
                ->addColumn('action', function ($data) {
                    return view('admin.kunyo-currency.partials.datatable-actions', compact('data'))->render();
                })
                ->addColumn('status', function ($data) {
                     return view('admin.kunyo-currency.partials.status', compact('data'))->render();
                })
                ->addColumn('created_at', function ($data) {
                    return $data->created_at . " <code>{$data->created_at->diffForHumans()}</code>";
                })
                ->addColumn('amount', function ($data) {
                    return 'Rs. ' . nrp($data->amount - $data->service_charge);
                })
                ->addColumn('quantity', function ($data) {
                    return nrp($data->quantity, 0);
                })
                ->rawColumns([ 'buyer', 'status', 'action', 'created_at', ])
                ->make(true);
        }
        $data = [];
        $data['sales'] = $this->kunyoCurrencyService->salesStats();
        $data['balance'] = app(UserService::class)->countCurrency();

        return view('admin.kunyo-currency.index', compact('data'));
    }

    /**
     * Display the specified resource.
     *
     * @param  KunyoCurrency $kunyoCurrency
     * @return Factory
     */
    public function show(KunyoCurrency $kunyoCurrency)
    {
        $data = [];
        $data['kunyo-currency'] = $kunyoCurrency;
        return view('admin.kunyo-currency.show', compact('data'));
    }

    /**
     * Get List of Currency Buyers (Users)
     *
     * @param Request $request
     * @return mixed
     * @throws Exception
     */
    public function users(Request $request)
    {
        return datatables()
            ->of($this->kunyoCurrencyService->filterOwners($request->input('search.value')))
            ->addColumn('user_name', function ($data) {
                return view('admin.order.partials.user-link', ['data' => $data, 'fullName' => $data->user_name])->render();
            })
            ->addColumn('balance_currency', function ($data) {
                return $data->kunyo_currency;
            })
            ->addColumn('last_transaction_date', function ($data) {
                $lastOrder = Carbon::parse($data->last_order_date);
                return $data->last_order_date . " <code>{$lastOrder->diffForHumans()}</code>";
            })
            ->rawColumns([ 'user_name', 'last_transaction_date', ])
            ->make(true);
    }
}
