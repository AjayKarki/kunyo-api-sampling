<?php

namespace App\Http\Controllers\Admin;

use Foundation\Lib\Role as RoleName;
use Exception;
use Foundation\Services\PermissionService;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Foundation\Models\Role;
use Neputer\Supports\BaseController;
use Foundation\Requests\Role\{
    StoreRequest,
    UpdateRequest
};
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\Factory;
use Foundation\Services\RoleService;

/**
 * Class RoleController
 * @package App\Http\Controllers\Admin
 */
class RoleController extends BaseController
{

    /**
     * The RoleService instance
     *
     * @var $roleService
     */
    private $roleService;
    /**
     * @var PermissionService
     */
    private $permissionService;

    public function __construct(RoleService $roleService, PermissionService $permissionService)
    {
        $this->roleService = $roleService;
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
                ->of($this->roleService->filter($request->input('search.value')))
                ->addColumn('name', function ($data) {
                    return $data->full_name ?? $data->name;
                })
                ->addColumn('created_at', function ($data) {
                    return view('admin.common.created-at',compact('data'))->render();
                })
                ->addColumn('action', function ($data) {
                    $model = 'role';
                    return view('admin.common.data-table-action', compact('data', 'model'))->render();
                })
                ->addColumn('status', function ($data) {
                     return view('admin.common.status', compact('data'))->render();
                })
                ->rawColumns([ 'action', 'created_at', 'status'])
                ->make(true);
        }

        $data['status'] = $this->roleService->status();
        return view('admin.role.index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Factory
     */
    public function create()
    {
        $data = [];
        $data['permissions'] = $this->permissionService->getPermissionList();
        $data['roles'] = $this->roleService->getParentRoles()->pluck('name', 'id');
        return view('admin.role.create', compact('data'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  StoreRequest $request
     * @return RedirectResponse
     */
    public function store(StoreRequest $request)
    {
        if(!$request->get('parent_id'))
            $request->merge(['parent_id' => 0]);

        $role = $this->roleService->new($request->all());

        if($role){
            $role->permissions()->sync((array) $request->get('permissions'));
        }
        flash('success', 'Record successfully created.');
        return $this->redirect($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  Role $role
     * @return Factory
     */
    public function show(Role $role)
    {
        $data = [];
        $data['role'] = $role;
        $data['role_permissions'] = $this->permissionService->getPermissionsForRole($role);
        return view('admin.role.show', compact('data'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Role $role
     * @return Factory
     */
    public function edit(Role $role)
    {
        $data = [];
        $data['role']  = $role;
        $data['role_permissions'] = $this->permissionService->getPermissionsForRole($role);
        $data['permissions'] = $this->permissionService->getPermissionList($role->parent_id != 0 ? $role->parent_id : '');
        if(! in_array($role->slug, RoleName::$current)){
            $data['roles'] = $this->roleService->getParentRoles()->pluck('name', 'id');
        }
        return view('admin.role.edit', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UpdateRequest  $request
     * @param  Role $role
     * @return RedirectResponse
     */
    public function update(UpdateRequest $request, Role $role)
    {
        if(!$request->get('parent_id'))
            $request->merge(['parent_id' => 0]);

        $this->roleService->update($request->all(), $role);
        $role->permissions()->sync((array) $request->get('permissions'));
        flash('success', 'Record successfully updated.');
        return $this->redirect($request);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Role $role
     * @return RedirectResponse
     */
    public function destroy(Role $role)
    {
        $this->roleService->delete($role);
        flash('success', 'Role is deleted successfully !');
        return redirect()->back();
    }

    /**
     * Get the list of permissions for the given role Id
     *
     * @param $roleId
     * @return mixed
     * @throws \Throwable
     */
    public function getPermissionsForRole($roleId)
    {
        $data['permissions'] = $this->permissionService->getPermissionList($roleId);
        return view('admin.role.partials.permission-assign', compact('data'))->render();
    }
}
