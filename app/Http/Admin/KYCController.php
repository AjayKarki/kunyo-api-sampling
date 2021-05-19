<?php

namespace App\Http\Controllers\Admin;

use Exception;
use Foundation\Events\KYCRespond;
use Foundation\Lib\KYC as KYCLib;
use Foundation\Models\User;
use Foundation\Services\ImageService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Foundation\Models\KYC;
use Neputer\Supports\BaseController;
use Foundation\Requests\KYC\{
    StoreRequest,
    UpdateRequest
};
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\Factory;
use Foundation\Services\KYCService;

/**
 * Class KYCController
 * @package App\Http\Controllers\Admin
 */
class KYCController extends BaseController
{

    /**
     * The KYCService instance
     *
     * @var $kYCService
     */
    private $kYCService;
    /**
     * @var ImageService
     */
    private $imageService;

    /**
     * KYCController constructor.
     * @param KYCService $kYCService
     * @param ImageService $imageService
     */
    public function __construct(KYCService $kYCService, ImageService $imageService)
    {
        $this->kYCService = $kYCService;
        $this->imageService = $imageService;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            return datatables()
                ->of($this->kYCService->filter($request->only('search.value', 'filter')))
                ->addColumn('user', function ($data) {
                    return "<a href='" . route('admin.user.show', $data->user_id) . "' target='_blank'>" . $data->user->getFullName() . "</a>";
                })
                ->addColumn('contact', function ($data) {
                    return "<b> {$data->email} <br> {$data->phone} </b>";
                })
                ->addColumn('roles', function ($data) {
                    return view('admin.kyc.partials.roles', compact('data'))->render();
                })
                ->addColumn('created_at', function ($data) {
                    return $data->created_at . " <code>{$data->created_at->diffForHumans()}</code>";
                })
                ->addColumn('action', function ($data) {
                    return "<a href='" . route('admin.kyc.show', $data->user_id) . "' title='View KYC Details'> <i class='fa fa-eye btn btn-success btn-xs'></i></a>";
                })
                ->addColumn('status', function ($data) {
                    return view('admin.kyc.partials.status', compact('data'))->render();
                })
                ->rawColumns(['user', 'contact', 'action', 'created_at', 'status', 'roles', ])
                ->make(true);
        }

        $data['status'] = $this->count($request);
        return view('admin.kyc.index', compact('data'));
    }

    /**
     * Show KUC Details for a User
     *
     * @param User $user
     * @return Application|Factory|View
     */
    public function show(User $user)
    {
        if(!$user->kyc){
            abort(404, 'KYC Not Found');
        }
        $data['user']  = $user->load('kyc');
        return view('admin.kyc.show', compact('data'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  StoreRequest $request
     * @return RedirectResponse
     */
    public function store(StoreRequest $request)
    {
        $kyc = $this->kYCService->new($request->except('document') + ['verification_status' => KYCLib::STATUS_SUBMITTED]);
        $this->imageService->insert([$request->file('document')['front']], $kyc, 'kyc', 'document_front');
        $this->imageService->insert([$request->file('document')['back']], $kyc, 'kyc', 'document_back');
        flash('success', 'Record successfully created.');
        return redirect()->back();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param User $user
     * @return Factory
     */
    public function edit(User $user)
    {
        $data = [];
        if($user->kyc){
            $data['user']  = $user->load('kyc');
            return view('admin.kyc.edit', compact('data'));
        }
        $data['user'] = $user;
        return view('admin.kyc.create', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateRequest $request
     * @param User $user
     * @return RedirectResponse
     */
    public function update(UpdateRequest $request, User $user)
    {
        $kyc = $user->kyc;
        $this->kYCService->update($request->except('document'), $kyc);
        if($request->hasFile('document.front')){
            $image = $this->imageService->getWhere($kyc, ['info' => 'document_front']);
            $this->imageService->remove($image);
            $this->imageService->insert([$request->file('document')['front']], $kyc, 'kyc', 'document_front');
        }
        if($request->hasFile('document.back')){
            $image = $this->imageService->getWhere($kyc, ['info' => 'document_back']);
            $this->imageService->remove($image);
            $this->imageService->insert([$request->file('document')['back']], $kyc, 'kyc', 'document_back');
        }
        flash('success', 'Record successfully updated.');
        return redirect()->route('admin.user.show', $user->id);
    }

    /**
     * Respond to KYC Verification Request
     *
     * @param Request $request
     * @param User $user
     * @return RedirectResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function respond(Request $request, User $user)
    {
        $this->validate($request, [
            'verification_status' => 'required|numeric',
            'remarks' => 'required_if:verification_status,' . KYCLib::STATUS_REJECTED
        ],
        [
            'remarks.required_if' => 'Please Provide the Reason for Rejection'
        ]);

        $kyc = $user->kyc;
        if(!$kyc)
            abort(404);

        $verified = [ 'verified_at' => $request->get('verification_status') == KYCLib::STATUS_VERIFIED ? now() : null];
        $this->kYCService->update($request->only('verification_status', 'remarks') + $verified, $kyc);

        event(new KYCRespond($kyc));
        flash('success', 'Application ' . KYCLib::$status[$request->get('verification_status')]);
        return redirect()->back();
    }

    /**
     * Count KYC Records by Verification Status
     *
     * @param Request $request
     * @return mixed
     */
    public function count(Request $request)
    {
        return $this->kYCService->getCountByStatus($request);
    }
}
