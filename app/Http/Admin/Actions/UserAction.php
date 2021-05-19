<?php


namespace App\Http\Controllers\Admin\Actions;

use App\Foundation\Lib\History;
use Foundation\Models\User;
use Foundation\Services\HistoryService;
use Foundation\Services\RoleService;
use Foundation\Services\UserService;
use Illuminate\Http\Request;
use Neputer\Supports\BaseController;

/**
 * Class UserAction
 * @package App\Http\Controllers\Admin\Actions
 */
class UserAction extends BaseController
{
    /**
     * @var UserService
     */
    private $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Sync User Roles
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function syncRoles(Request $request)
    {
        $this->validate($request, [
            'user_id' => 'required',
            'roles' => 'required|array'
        ]);

        $user = $this->userService->findOrFail($request->get('user_id'));

        $oldRoles = $user->roles->pluck('name', 'id');
        $newRoles = app(RoleService::class)->getNameFromId($request->get('roles'));

        if(!($oldRoles == $newRoles)){
            $user->roles()->sync($request->get('roles'));

            app(HistoryService::class)->new([
                'title' => 'User roles Updated',
                'information' => 'User\'s roles are updated.',
                'user_id' => auth()->id(),
                'user_name' => auth()->user()->getFullName(),
                'historyable_type' => User::class,
                'historyable_id' => $request->get('user_id'),
                'old_value' => $oldRoles,
                'new_value' => $newRoles,
                'type' => History::TYPE_USER_ROLE_UPDATED,
            ]);
        }

        return response()->json(['msg' => 'Roles Updated']);
    }

    /**
     * Blacklist Users
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function blacklist(Request $request)
    {
        $ids = explode(',', trim($request->get('ids'), ','));
        app(UserService::class)->blacklist($ids);
        flash('success', 'Selected Users are Blacklisted!');
        return redirect()->back();
    }


    /**
     * Remove Users from Blacklist
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function removeFromBlacklist(Request $request)
    {
        $ids = explode(',', trim($request->get('ids'), ','));
        app(UserService::class)->removeFromBlacklist($ids);
        flash('success', 'Selected Users are removed from Blacklisted!');
        return redirect()->back();
    }
}
