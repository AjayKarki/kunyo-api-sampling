<?php

namespace App\Http\Controllers\Admin;

use Exception;
use Foundation\Services\RegionService;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Foundation\Models\Region;
use Neputer\Supports\BaseController;
use Foundation\Requests\Region\{
    StoreRequest,
    UpdateRequest
};
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\Factory;

/**
 * Class RegionController
 * @package App\Http\Controllers\Admin
 */
class RegionController extends BaseController
{

    /**
     * The RegionService instance
     *
     * @var $regionService
     */
    private $regionService;

    public function __construct(RegionService $regionService)
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
                ->addColumn('created_at', function ($data) {
                    return $data->created_at . " <code>{$data->created_at->diffForHumans()}</code>";
                })
                ->addColumn('action', function ($data) {
                    $model = 'region';
                    return view('admin.common.data-table-action', compact('data', 'model'))->render();
                })
                ->addColumn('status', function ($data) {
                     return view('admin.common.status', compact('data'))->render();
                })
                ->addColumn('checkbox', function ($data) {
                    return view('admin.common.checkbox', compact('data'))->render();
                })
                ->rawColumns([ 'action', 'created_at', 'status', 'checkbox' ])
                ->make(true);
        }

        return view('admin.region.index');
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
        return view('admin.region.create', compact('data'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  StoreRequest $request
     * @return RedirectResponse
     */
    public function store(StoreRequest $request)
    {
        $this->regionService->new($request->all());
        flash('success', 'Record successfully created.');
        return $this->redirect($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  Region $region
     * @return Factory
     */
    public function show(Region $region)
    {
        $data = [];
        $data['region'] = $region;
        return view('admin.region.show', compact('data'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Region $region
     * @return Factory
     */
    public function edit(Region $region)
    {
        $data = [];
        $data['region']  = $region;
        return view('admin.region.edit', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UpdateRequest  $request
     * @param  Region $region
     * @return RedirectResponse
     */
    public function update(UpdateRequest $request, Region $region)
    {
        $this->regionService->update($request->all(), $region);
        flash('success', 'Record successfully updated.');
        return $this->redirect($request);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Region $region
     * @return RedirectResponse
     */
    public function destroy(Region $region)
    {
        $this->regionService->delete($region);
        flash('success', 'Region is deleted successfully !');
        return redirect()->back();
    }
}
