<?php


namespace Foundation\DataTables;

/**
 * Class UserDatatable
 * @package Foundation\DataTables
 */
final class UserDatatable
{
    /**
     * Get User Datatable
     *
     * @param $user
     * @param null $role
     * @return mixed
     * @throws \Exception|\Throwable
     */
    public static function get($user, $role = null)
    {
        return datatables()
            ->of($user)
            ->filter(function ($query) use ($role) {
                if ($keyword = request()->input('search.value')) {
                    $query->whereLike(['email', 'phone_number'], $keyword);
                }
                $query->whereLike('roles.slug', $role);
            })
            ->addColumn('view_summary_pickers', function ($data) {
                return view('admin.user.partials.view_summary_pickers', compact('data'));
            })
            ->addColumn('is_verified', function ($data) {
                return view('admin.user.partials.is_verified', compact('data'));
            })
            ->addColumn('full_name', function ($data) {
                return view('admin.user.partials.user-link', compact('data'))->render();
            })
            ->addColumn('image', function ($data) {
                return view('admin.user.partials.users_datatable_image', compact('data'))->render();
            })
            ->addColumn('email', function ($data) {
                return '<b>' . $data->email . '<br>' . optional($data)->phone_number . '</b>';
            })
            ->addColumn('is_deactivated', function ($data) {
                return view('admin.user.partials.is_deactivated', compact('data'))->render();
            })
            ->addColumn('created_at', function ($data) {
                return view('admin.common.created-at', compact('data'))->render();
            })
            ->addColumn('action', function ($data) use ($role) {
                $model = 'user';
                return view('admin.common.data-table-common-action', compact('data', 'model'))->render();
            })
            ->addColumn('checkbox', function ($data) {
                return view('admin.common.checkbox', compact('data'))->render();
            })
            ->addColumn('status', function ($data) {
                return view('admin.common.status', compact('data'))->render();
            })
            ->rawColumns(['checkbox', 'action', 'created_at', 'is_deactivated', 'full_name', 'roles', 'image', 'email', 'is_verified', ])
            ->make(true);
    }

}
