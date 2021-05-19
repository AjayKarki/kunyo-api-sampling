<?php

namespace Foundation\Lib;

use App\Foundation\Lib\History;
use Foundation\Models\User;
use Foundation\Services\UserService;
use Foundation\Services\HistoryService;

/**
 * Class Impersonate
 * @package Foundation\Lib
 */
final class Impersonated
{

    const IMPERSONATED = 'impersonated';
    const IMPERSONATER = 'impersonater';
    const REMOVED_ROLES = 'impersonated-roles';

    /**
     * Return original user
     *
     * @return User|Authenticatable
     */
    public static function originalUser()
    {
        return User::find(static::getOriginalID()) ?? auth()->user();
    }

    /**
     * Impersonate the given user
     *
     * @param User $impersonate
     * @param null $as
     * @return User|null
     */
    public static function impersonate(User $impersonate, $as = null)
    {
        $user = self::handleRoles($impersonate, $as);

        app(HistoryService::class)
            ->new([
                'title' => 'User Impersonated',
                'information' =>  '('.auth()->id().')' . optional(auth()->user())->getFullName() . ' is impersonating '. '('.optional($user)->id.')' .optional($user)->getFullName(),
                'user_id' => auth()->id(),
                'user_name' => auth()->user()->getFullName(),
                'historyable_type' => User::class,
                'historyable_id' => $user->id,
                'type' => History::TYPE_IMPERSONATION,
            ]);

        if ($user->id !== ($original = auth()->id())) {
            session()->put(static::IMPERSONATED, $impersonate->id);
            session()->put(static::IMPERSONATER, $original);
        }
        return $user;
    }
    /**
     * Stop impersonating
     *
     * return void
     */
    public static function stopImpersonating()
    {
        self::restoreRoles();

        $user = User::query()
            ->where('id', Impersonated::getID())
            ->first();

        app(HistoryService::class)
            ->new([
                'title' => 'User Impersonated Stopped',
                'information' =>  '('.auth()->id().')' . optional(auth()->user())->getFullName() . ' stopped impersonating '. '('.Impersonated::getID().')' .optional($user)->getFullName(),
                'user_id' => auth()->id(),
                'user_name' => auth()->user()->getFullName(),
                'historyable_type' => User::class,
                'historyable_id' => optional($user)->id,
                'type' => History::TYPE_IMPERSONATION,
            ]);
        session()->forget(static::IMPERSONATED);
    }
    /**
     * If the user is impersonating
     *
     * @return bool
     */
    public static function isImpersonating()
    {
        return session()->has(static::IMPERSONATED);
    }
    /**
     * Return the impersonated user id
     *
     * @return mixed
     */
    public static function getID()
    {
        return session()->get(static::IMPERSONATED);
    }
    /**
     * Return the original user id
     *
     * @return mixed
     */
    public static function getOriginalID()
    {
        return session()->get(static::IMPERSONATER);
    }

    /**
     * For user having multiple roles.
     *
     * @param User $user
     * @param $role
     * @return User|null
     */
    private static function handleRoles(User $user, $role)
    {
        if ($role){
            $otherRoles = $user->roles->where('slug', '!=', $role)->pluck('id')->toArray();
            $selectedRole = $user->roles->where('slug', $role)->pluck('id');
            session()->put(static::REMOVED_ROLES, $otherRoles);
            $user->roles()->sync($selectedRole);
            $user = $user->fresh();
        }
        return $user;
    }

    /**
     * Restore User's Original Roles on Stop Impersonation
     *
     */
    private static function restoreRoles()
    {
        $user = app(UserService::class)->findOrFail(self::getID());
        $roles = array_merge(session()->get(self::REMOVED_ROLES) ?? [], $user->roles->pluck('id')->toArray());
        $user->roles()->sync($roles);
    }

}
