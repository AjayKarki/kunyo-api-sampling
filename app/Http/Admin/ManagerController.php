<?php

namespace App\Http\Controllers\Admin;

use Foundation\DataTables\UserDatatable;
use Foundation\Lib\Role;
use Foundation\Models\User;
use Illuminate\Http\Request;
use Neputer\Supports\Mixins\Image;
use Foundation\Services\RoleService;
use Foundation\Services\UserService;
use Neputer\Supports\BaseController;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\Factory;
use Foundation\Requests\User\StoreRequest;
use Foundation\Requests\User\UpdateRequest;

class ManagerController extends BaseController
{

    use Image;

    /**
     * The UserService instance
     *
     * @var $userService
     */
    private $userService;

    /**
     * @var RoleService
     */
    private $roleService;

    private $folder = 'user';

    public function __construct(
        UserService $userService,
        RoleService $roleService)
    {
        $this->userService = $userService;
        $this->roleService = $roleService;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|mixed
     * @throws \Exception
     */
    public function index(Request $request)
    {
        $role = Role::$current[Role::ROLE_MANAGER];
        if ($request->ajax()) {
            return UserDatatable::get(
                $this->userService->filterByRole($request->only('search.value', 'filter'), $role), $role);
        }
        $data['roles'] = $this->roleService->getRoles();
        $data['status'] = $this->userService->getCountByStatus($role);
        return view('admin.manager.index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Factory
     */
    public function create()
    {
        $data = [];
        $data['roles'] = $this->roleService->getRoles();
        return view('admin.manager.create', compact('data'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreRequest $request
     * @return RedirectResponse
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function store(StoreRequest $request)
    {
        $user = $this->userService->new($request->merge([
            'password' => bcrypt($request->get('password')),
            'image' => $request->has('photo') ? $this->uploadImage($request->file('photo'), $this->folder) : null,
            'is_verified' => 1,
        ])->all());

        if ($user) {
            $user->roles()->sync((array) $this->roleService->getId(Role::$current[Role::ROLE_MANAGER]));
        }

        flash('success', 'User successfully created.');
        return $this->redirect($request);
    }

    /**
     * @param Request $request
     * @param User $user
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Exception
     */
    public function show(Request $request, User $user)
    {
        $data = [];
        $data['user'] = $user;
        return view('admin.manager.show', compact('data'));
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
        $data['user'] = $user;
        $data['roles'] = $this->roleService->getRoles();

        return view('admin.manager.edit', compact('data'));
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
        if ($request->get('password')) {
            $request = $request->merge([
                'password' => bcrypt($request->get('password'))
            ]);
        }
        $request = $request->merge([
            'image' => $request->has('photo') ? $this->uploadImage($request->file('photo'), $this->folder, $user->image) : $user->image,
            'is_verified' => 1,
        ]);
        $this->userService->update(array_filter($request->all()), $user);
        $user->roles()->sync((array) $this->roleService->getId(Role::$current[Role::ROLE_MANAGER]));
        flash('success', 'Record successfully updated.');
        return $this->redirect($request);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param User $user
     * @return RedirectResponse
     */
    public function destroy(User $user)
    {
        $this->userService->delete($user);
        flash('success', 'Record is deleted successfully !');
        return redirect('admin/manager');
    }

}
