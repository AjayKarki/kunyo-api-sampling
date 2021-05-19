<?php

namespace App\Http\Controllers\Admin;

use App\Foundation\Lib\AssignAmount as AssignAmountLib;
use Carbon\Carbon;
use Exception;
use Foundation\Services\RoleService;
use Foundation\Services\UserService;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Foundation\Models\AssignAmount;
use Neputer\Supports\BaseController;
use Foundation\Requests\AssignAmount\{
    StoreRequest,
    UpdateRequest
};
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\Factory;
use Foundation\Services\AssignAmountService;

/**
 * Class AssignAmountController
 * @package App\Http\Controllers\Admin
 */
class AssignAmountController extends BaseController
{

    /**
     * The AssignAmountService instance
     *
     * @var $assignAmountService
     */
    private $assignAmountService;

    public function __construct(AssignAmountService $assignAmountService)
    {
        $this->assignAmountService = $assignAmountService;
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
                ->of($this->assignAmountService->filter($request->only('search.value', 'filter')))
                ->addColumn('assignee', function ($data) {
                    return '<b>' . $data->assignee->first_name . ' ' . $data->assignee->last_name . ' </b> <br>' . $data->assignee->email;
                })
                ->addColumn('assigned_amount', function ($data) {
                    return 'Rs. ' . nrp($data->assigned_amount);
                })
                ->addColumn('gift_card_spend', function ($data) {
                    return 'Rs. ' . nrp($data->gift_card_spend);
                })
                ->addColumn('top_up_spend', function ($data) {
                    return 'Rs. ' . nrp($data->top_up_spend);
                })
                ->addColumn('remaining_amount', function ($data) {
                    return 'Rs. ' . nrp($data->remaining_amount);
                })
                ->addColumn('last_assigned', function ($data) {
                    $date = Carbon::parse($data->last_assigned_date);
                    return $date->format('d M, Y H:i A') . ' <code>' . $date->diffForHumans() . '</code>';
                })
                ->addColumn('action', function ($data) {
                    $model = 'assign-amount';
                    return view('admin.assign-amount.partials.datatable-actions', compact('data', 'model'))->render();
                })
                ->rawColumns(['assignee', 'action', 'last_assigned', ])
                ->make(true);
        }

        $data = [];
        $data['roles'] = app(RoleService::class)->getRolesWithSlug();

        return view('admin.assign-amount.index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request)
    {
        $data = app(UserService::class)->searchByRole($request->get('search'), $request->get('role'));
        return response()->json($data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  StoreRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreRequest $request)
    {
        $this->assignAmountService->new([
            'user_id' => $request->get('user_id'),
            'credit' => $request->get('credit'),
            'type' => AssignAmountLib::TYPE_CREDIT,
            'assigned_by' => auth()->id(),
            'created_at' => Carbon::parse($request->get('created_at'))
        ]);
        return response()->json(['message' => 'Amount Assigned']);
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @param $userId
     * @return \Illuminate\Contracts\Foundation\Application|Factory|\Illuminate\Http\JsonResponse|View
     * @throws \Throwable
     */
    public function show(Request $request, $userId)
    {
        if ($request->ajax()){
            $amounts = $this->assignAmountService->findByUserId($userId, $request->get('start_date'), $request->get('end_date'));
            $total = [
                'credit' => 0,
                'gc_debit' => 0,
                'tu_debit' => 0,
            ];
            foreach ($amounts as $amount){
                $total['credit'] += $amount->sum('credit');
                $total['gc_debit'] += $amount->where('order_type', \Foundation\Lib\Product::PRODUCT_GIFT_CARD_INDEX)->sum('debit');
                $total['tu_debit'] += $amount->where('order_type', \Foundation\Lib\Product::PRODUCT_TOP_UP_INDEX)->sum('debit');
            }

            $total['remaining'] = $total['credit'] - $total['gc_debit'] - $total['tu_debit'];

            $total = array_map(function ($value) { return nrp($value); }, $total);

            $data = [
                'amounts' => view('admin.assign-amount.partials.show-list', compact('amounts'))->render(),
                'total' => $total
            ];

            return response()->json($data);
        }
        return view('admin.assign-amount.show', compact('userId'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $userId
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($userId)
    {
        $this->assignAmountService->delete($userId);
        return response()->json(['message' => 'User Details Deleted!']);
    }
}
