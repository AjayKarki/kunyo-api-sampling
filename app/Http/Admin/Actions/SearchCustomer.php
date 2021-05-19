<?php

namespace App\Http\Controllers\Admin\Actions;

use Illuminate\Http\Request;
use Neputer\Supports\Mixins\Responsable;

/**
 * Class SearchCustomer
 * @package App\Http\Controllers\Admin\Actions
 */
final class SearchCustomer
{

    use Responsable;

    /**
     * @param Request $request
     * @return mixed
     */
    public function __invoke(Request $request)
    {
        $data = [];

        if ($term = $request->input('term.term')) {
            $data = app('db')
                ->table('users')
                ->select('users.id')
                ->selectRaw("CONCAT(COALESCE(first_name,''),' ',COALESCE(middle_name,''),' ',COALESCE(last_name,''),' | ', COALESCE(email,'')) AS name")
                ->where('email', 'like', '%' . $term . '%')
                ->join('role_user', 'role_user.user_id', '=', 'users.id')
	            ->join('roles', 'roles.id', '=', 'role_user.role_id')
	            ->where('roles.slug', 'customer')
                ->limit(10)
                ->get() ;
        }

        return $this->responseOk($data);
    }

}
