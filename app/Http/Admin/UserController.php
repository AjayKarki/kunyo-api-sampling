<?php

namespace App\Http\Controllers\Admin;

use App\Foundation\Lib\History;
use Exception;
use Foundation\Services\HistoryService;
use Foundation\Services\RoleService;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Foundation\Models\User;
use Neputer\Supports\BaseController;
use Neputer\Supports\Mixins\Image;
use Foundation\Requests\User\{
    StoreRequest,
    UpdateRequest
};
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\Factory;
use Foundation\Services\UserService;

/**
 * Class UserController
 * @package App\Http\Controllers\Admin
 */
class UserController extends BaseController
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

    private $questionSetService;
    private $folder = 'user';

    public function __construct(
        UserService $userService,
        RoleService $roleService)
    {
        $this->userService = $userService;
        $this->roleService = $roleService;
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
            $user = $this->userService->filter($request->only('search.value', 'filter'));
            return $this->getDataTable($user);
        }
        $data['roles'] = $this->roleService->getRoles();
        $data['status'] = $this->userService->getCountByStatus();
        return view('admin.user.index', compact('data'));
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
        return view('admin.user.create', compact('data'));
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
            'is_verified' => 1,
            'password' => bcrypt($request->get('password')),
            'image' => $request->has('photo') ? $this->uploadImage($request->file('photo'), $this->folder) : null,
        ])->all());

        if ($user) {
            $user->roles()->sync((array)$request->get('roles'));
        }

        flash('success', 'User successfully created.');
        return $this->redirect($request);
    }

    /**
     * @param User $user
     * @param Request $request
     * @return Factory|View
     */
    public function show(User $user, Request $request)
    {
        $data = [];
        $data['user'] = $user;
        return view('admin.user.show', compact('data'));
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

        return view('admin.user.edit', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateRequest $request
     * @param User $user
     * @return RedirectResponse
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function update(UpdateRequest $request, User $user)
    {
        $imageName = $request->has('photo') ? $this->uploadImage($request->file('photo'), $this->folder, $user->image) : $user->image;
        if ($request->get('password')) {
            $request = $request->merge([
                'password' => bcrypt($request->get('password'))
            ]);
        }
        $request = $request->merge([
            'is_verified' => 1,
            'image' => $imageName,
        ]);
        $this->userService->update(array_filter($request->all()), $user);

        $oldRoles = $user->roles->pluck('name', 'id');
        $newRoles = app(RoleService::class)->getNameFromId($request->get('roles'));

        if (!($oldRoles == $newRoles)) {
            $user->roles()->sync((array)$request->get('roles'));
            app(HistoryService::class)->new([
                'title' => 'User roles Updated',
                'information' => 'User\'s roles are updated.',
                'user_id' => auth()->id(),
                'user_name' => auth()->user()->getFullName(),
                'historyable_type' => User::class,
                'historyable_id' => $user->id,
                'old_value' => $oldRoles,
                'new_value' => $newRoles,
                'type' => History::TYPE_USER_ROLE_UPDATED,
            ]);
        }
        flash('success', 'Record successfully updated.');
        return $this->redirect($request);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param User $user
     * @return void
     * @throws Exception
     */
    public function destroy(Request $request, User $user)
    {
        $this->userService->delete($user);
        flash('success', 'Record is deleted successfully !');
        return;
    }

    /**
     * Get Datatable for the passed User
     *
     * @param $user
     * @return mixed
     * @throws Exception
     */
    protected function getDataTable($user)
    {
        return datatables()
            ->of($user)
            ->addColumn('full_name', function ($data) {
                return view('admin.user.partials.user-link', compact('data'))->render();
            })
            ->addColumn('image', function ($data) {
                return view('admin.user.partials.users_datatable_image', compact('data'))->render();
            })
            ->addColumn('email', function ($data) {
                return '<b>' . $data->email . '<br>' . optional($data)->phone_number . '</b>';
            })
            ->addColumn('roles', function ($data) {
                $value = '';
                $ids = [];
                foreach ($data->roles as $role) {
                    $value .= "<code>" . $role->name . "</code> | ";
                    array_push($ids, $role->id);
                }
                return view('admin.user.partials.show-roles', compact('value', 'ids', 'data'))->render();
            })
            ->addColumn('created_at', function ($data) {
                return view('admin.common.created-at', compact('data'))->render();
            })
            ->addColumn('action', function ($data) {
                $model = 'user';
                return view('admin.common.data-table-common-action', compact('data', 'model'))->render();
            })
            ->addColumn('checkbox', function ($data) {
                return view('admin.common.checkbox', compact('data'))->render();
            })
            ->addColumn('status', function ($data) {
                return view('admin.common.status', compact('data'))->render();
            })
            ->rawColumns(['checkbox', 'action', 'created_at', 'full_name', 'roles', 'image', 'email',])
            // ->removeColumn('password')
            ->make(true);
    }

}
