<?php

namespace App\Http\Controllers\Admin;

use App\Foundation\Lib\Referral as ReferralLib;
use Exception;
use Foundation\Services\OrderService;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Foundation\Models\Referral;
use Modules\Payment\PaymentService;
use Neputer\Supports\BaseController;
use Foundation\Requests\Referral\{
    StoreRequest,
    UpdateRequest
};
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\Factory;
use Foundation\Services\ReferralService;

/**
 * Class ReferralController
 * @package App\Http\Controllers\Admin
 */
class ReferralController extends BaseController
{

    /**
     * The ReferralService instance
     *
     * @var $referralService
     */
    private $referralService;

    public function __construct(ReferralService $referralService)
    {
        $this->referralService = $referralService;
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
                ->of($this->referralService->filter($request->only('search.value', 'filter')))
                ->addColumn('created_at', function ($data) {
                    return $data->created_at . " <code>{$data->created_at->diffForHumans()}</code>";
                })
                ->addColumn('action', function ($data) {
                    return view('admin.referral.partials.actions', compact('data'))->render();
                })
                ->addColumn('status', function ($data) {
                     return view('admin.common.status', compact('data'))->render();
                })
                ->addColumn('referrals_count', function ($data) {
                    return " <span class='text-danger' title='Total'>{$data->referral_list_count}</span> | <span class='text-success' title='Verified'>{$data->referral_list_count}</span>";
                })
                ->addColumn('user', function ($data) {
                    return "<a href='" . route('admin.user.show', $data->user_id) . "' target='_blank'>{$data->user_name}</a>";
                })
                ->addColumn('link', function ($data) {
                    return "<code>{$data->link}</code>";
                })
                ->rawColumns([ 'action', 'created_at', 'referrals_count', 'user', 'link', 'status', ])
                ->make(true);
        }

        $data = [];
        $data['referrals'] = app(ReferralService::class)->count();
        $data['referral_user'] = app(ReferralService::class)->countList();
        return view('admin.referral.index', compact('data'));
    }

    /**
     * Display the specified resource.
     *
     * @param $referral
     * @return Factory
     * @throws Exception
     */
    public function show($referral)
    {
        if (request()->ajax()) {
            return datatables()
                ->of($this->referralService->filterList($referral, request()->only('search.value')))
                ->addColumn('created_at', function ($data) {
                    return $data->created_at . " <code>{$data->created_at->diffForHumans()}</code>";
                })
                ->addColumn('status', function ($data) {
                    return view('admin.referral.partials.status', compact('data'))->render();
                })
                ->addColumn('user', function ($data) {
                    return "<a href='" . route('admin.user.show', $data->user_id) . "' target='_blank'>{$data->user_name}</a>";
                })
                ->addColumn('amount_spent', function ($data) {
                    return '<code class="text-success">Rs. ' . nrp($data->amount_spent) . '</code>';
                })
                ->addColumn('settled', function ($data) {
                    return "<span class='badge badge-" . ($data->is_used ? "success" : "danger") . "'>" . ($data->is_used ? 'Settled' : 'Not Settled') . "</span>";
                })
                ->rawColumns([ "created_at", 'user', 'status', 'amount_spent', 'settled' ])
                ->make(true);
        }

        $data = [];
        $data['referral'] = app(ReferralService::class)->findOrFail($referral);

        $totalUsers = app(ReferralService::class)->getListWhere([
            'referral_id' => $referral,
            'status' => ReferralLib::STATUS_VERIFIED,
        ]);

        $unsettledUsers = app(ReferralService::class)->getListWhere([
            'referral_id' => $referral,
            'status' => ReferralLib::STATUS_VERIFIED,
            'is_used' => false
        ]);

        $data['referral_list'] = app(ReferralService::class)->countList($referral);
        $data['total_amount_spent'] = $totalUsers->sum('amount_spent');
        $data['unsettled_amount_spent'] = $unsettledUsers->sum('amount_spent');
        return view('admin.referral.show', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Referral $referral
     * @return RedirectResponse
     */
    public function update($referral)
    {
        $this->referralService->updateList([
                'status' => ReferralLib::STATUS_VERIFIED,
                'is_used' => false,
                'referral_id' => $referral
            ],
            [
                'is_used' => true
            ]
        );
        flash('success', 'All Commission Settled');
        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Referral $referral
     * @return RedirectResponse
     */
    public function destroy(Referral $referral)
    {
        $this->referralService->delete($referral);
        flash('success', 'Referral is deleted successfully !');
        return redirect()->back();
    }
}
