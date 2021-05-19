<?php

namespace App\Http\Controllers\Admin;

use Exception;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Foundation\Models\Platform;
use Neputer\Supports\BaseController;
use Foundation\Requests\Platform\{
    StoreRequest,
    UpdateRequest
};
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\Factory;
use Foundation\Services\PlatformService;

/**
 * Class PlatformController
 * @package App\Http\Controllers\Admin
 */
class PlatformController extends BaseController
{

    /**
     * The PlatformService instance
     *
     * @var $platformService
     */
    private $platformService;

    public function __construct(PlatformService $platformService)
    {
        $this->platformService = $platformService;
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
                ->of($this->platformService->filter($request->input('search.value')))
                ->addColumn('created_at', function ($data) {
                    return $data->created_at . " <code>{$data->created_at->diffForHumans()}</code>";
                })
                ->addColumn('action', function ($data) {
                    $model = 'platform';
                    return view('admin.common.data-table-action', compact('data', 'model'))->render();
                })
                ->addColumn('status', function ($data) {
                     return view('admin.common.status', compact('data'))->render();
                })
                ->addColumn('checkbox', function ($data) {
                    return view('admin.common.checkbox', compact('data'))->render();
                })
                ->rawColumns([ 'action', 'created_at', 'status', 'checkbox'])
                ->make(true);
        }

        return view('admin.platform.index');
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
        return view('admin.platform.create', compact('data'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  StoreRequest $request
     * @return RedirectResponse
     */
    public function store(StoreRequest $request)
    {
        $this->platformService->new($request->all());
        flash('success', 'Record successfully created.');
        return $this->redirect($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  Platform $platform
     * @return Factory
     */
    public function show(Platform $platform)
    {
        $data = [];
        $data['platform'] = $platform;
        return view('admin.platform.show', compact('data'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Platform $platform
     * @return Factory
     */
    public function edit(Platform $platform)
    {
        $data = [];
        $data['platform']  = $platform;
        return view('admin.platform.edit', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UpdateRequest  $request
     * @param  Platform $platform
     * @return RedirectResponse
     */
    public function update(UpdateRequest $request, Platform $platform)
    {
        $this->platformService->update($request->all(), $platform);
        flash('success', 'Record successfully updated.');
        return $this->redirect($request);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Platform $platform
     * @return RedirectResponse
     */
    public function destroy(Platform $platform)
    {
        $this->platformService->delete($platform);
        flash('success', 'Platform is deleted successfully !');
        return redirect()->back();
    }
}
