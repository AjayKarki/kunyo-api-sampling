<?php

namespace App\Http\Controllers\Admin;

use Neputer\Supports\Mixins\Image;
use Foundation\Services\RoleService;
use Neputer\Supports\BaseController;
use Foundation\Services\UserService;
use Foundation\Requests\User\UpdateRequest;

class ProfileController extends BaseController
{
    use Image;
    /**
     * @var UserService
     */
    public $userService;

    /**
     * ProfileController constructor.
     * @param UserService $userService
     */
    /**
     * @var RoleService
     */
    private $roleService;

    public function __construct(UserService $userService, RoleService $roleService)
    {
        $this->userService = $userService;
        $this->roleService = $roleService;

    }


    public function edit()
    {
        $data = [];
        $data['roles'] = $this->roleService->getRoles();
        $data['profile'] = $this->userService->getLoggedInUser();
        return view('admin.user.profile', compact('data'));

    }

    public function update(UpdateRequest $request)
    {
        $user = $this->userService->findOrFail(auth()->id());
        $request = $request->merge([
            'image' => $request->has('photo') ? $this->uploadImage($request->file('photo'), 'user', $user->image) : $user->image,
        ]);

        if($password = $request->get('password')) {
            $request->merge([
                'password' => bcrypt($password),
            ]);
        }
        $this->userService->updateLoggedInUser($user, array_filter($request->all()));
        flash('success', 'Record successfully updated.');
        return redirect()->route('admin.profile.edit');
    }
}
