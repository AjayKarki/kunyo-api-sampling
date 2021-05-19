<?php

namespace App\Http\Controllers\Admin;

use Exception;
use Foundation\Lib\Role;
use Foundation\Models\User;
use Illuminate\Http\Response;
use Foundation\Lib\Impersonated;
use Neputer\Supports\BaseController;
use Illuminate\Http\RedirectResponse;
use Illuminate\Session\Store as Session;

/**
 * Class ImpersonateController
 * @package App\Http\Controllers\Admin
 */
class ImpersonateController extends BaseController
{

    private $redirectToAdmin = '/spanel/dashboard';
    private $redirectToSummary = '/spanel/order/summary';

    private $redirectToUser = '/';

    /**
     * @var Session
     */
    private $session;

    /**
     * Create a new controller instance.
     *
     * @param Session $session
     * @throws Exception
     */
    public function __construct(
        Session $session
    )
    {
        $this->session = $session;
    }

    /**
     * Impersonate the given user.
     *
     * @param User $user
     * @param $as | Impersonate as 'role'
     * @return RedirectResponse
     */
    public function impersonate(User $user, $as = null)
    {
        $user = Impersonated::impersonate($user, $as);
        flash('success', $user->getFullName() .' is successfully impersonated.');

        return redirect()
            ->to(self::redirectTo($user));
    }
    /**
     * Revert to the original user.
     *
     * @return RedirectResponse
     */
    public function revert()
    {
        Impersonated::stopImpersonating();
        flash('success', 'Impersonation is successfully reverted .');

        return redirect()
            ->to($this->redirectToAdmin(auth()->user()));
    }

    /**
     * Return the redirect path according to the user role
     *
     * @param $user
     * @return string
     */
    private function redirectTo($user)
    {
        return $user->hasHighAccess() ? $this->redirectToAdmin($user) : $this->redirectToUser;
    }

    private function redirectToAdmin($user)
    {
        if ($user->hasRole(Role::getSuperAdmin(), Role::getAdmin())) {
            return $this->redirectToAdmin;
        }
        return $this->redirectToSummary;
    }

}
