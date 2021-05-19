<?php

namespace App\Http\Controllers\Admin;

use Exception;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Foundation\Models\Permission;
use Neputer\Supports\BaseController;
use Foundation\Requests\Permission\{
    StoreRequest,
    UpdateRequest
};
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\Factory;
use Foundation\Services\PermissionService;

/**
 * Class PermissionController
 * @package App\Http\Controllers\Admin
 */
class PermissionController extends BaseController
{

    /**
     * The PermissionService instance
     *
     * @var $permissionService
     */
    private $permissionService;

    public function __construct(PermissionService $permissionService)
    {
        $this->permissionService = $permissionService;
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
                ->of($this->permissionService->filter($request->input('search.value')))
                ->addColumn('created_at', function ($data) {
                    return $data->created_at . " <code>{$data->created_at->diffForHumans()}</code>";
                })
                ->addColumn('action', function ($data) {
                    $model = 'permission';
                    return view('admin.common.data-table-action', compact('data', 'model'))->render();
                })
                ->addColumn('status', function ($data) {
                     return view('admin.common.status', compact('data'))->render();
                })
                ->rawColumns([ 'action', 'created_at', 'status'])
                ->make(true);
        }
        $data['status'] = $this->permissionService->status();
        return view('admin.permission.index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Factory
     */
    public function create()
    {
        $data = [];
        return view('admin.permission.create', compact('data'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  StoreRequest $request
     * @return RedirectResponse
     */
    public function store(StoreRequest $request)
    {
        $this->permissionService->new($request->all());
        flash('success', 'Record successfully created.');
        return $this->redirect($request);
    }

    /**
     * Display the specified rmesource.
     *
     * @param  Permission $permission
     * @return Factory
     */
    public function show(Permission $permission)
    {
        $data = [];
        $data['permission'] = $permission;
        return view('admin.permission.show', compact('data'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Permission $permission
     * @return Factory
     */
    public function edit(Permission $permission)
    {
        $data = [];
        $data['permission']  = $permission;
        return view('admin.permission.edit', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UpdateRequest  $request
     * @param  Permission $permission
     * @return RedirectResponse
     */
    public function update(UpdateRequest $request, Permission $permission)
    {
        $this->permissionService->update($request->all(), $permission);
        flash('success', 'Record successfully updated.');
        return $this->redirect($request);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Permission $permission
     * @return RedirectResponse
     */
    public function destroy(Permission $permission)
    {
        $this->permissionService->delete($permission);
        flash('success', 'Permission is deleted successfully !');
        return redirect()->back();
    }
}
