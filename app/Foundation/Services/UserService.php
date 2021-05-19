<?php

namespace Foundation\Services;

use Carbon\Carbon;
use Foundation\Builders\Filters\User\Filter;
use Foundation\Lib\Product;
use Foundation\Lib\Role;
use Foundation\Lib\SoftDelete;
use Foundation\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Neputer\Config\Status;
use Neputer\Supports\BaseService;
use Neputer\Supports\Mixins\Image;

/**
 * Class UserService
 * @package Foundation\Services
 */
class UserService extends BaseService
{
//    use Image;
    /**
     * The User instance
     *
     * @var $model
     */
    protected $model;

    /**
     * UserService constructor.
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->model = $user;
    }

    /**
     * Filter
     *
     * @param array $data
     * @return mixed
     */
    public function filter(array $data)
    {
        $model = $this->model;

        switch ($data['filter']['soft_delete']) {
            case SoftDelete::ONLY_TRASHED :
                $model = $model->onlyTrashed();
                break;
            case SoftDelete::WITH_TRASHED :
                $model = $model->withTrashed();
                break;
        }

        return $model = Filter::apply(
            $model
                ->select('*'), $data)
            ->with('roles')
            ->orderBy('created_at', 'DESC');
    }

    /**
     * Get User's List by given $role
     *
     * @param array $data
     * @param $role
     * @return mixed
     */
    public function filterByRole(array $data, $role)
    {

        return Filter::apply(
            $this->model
                ->select('users.*')
                ->selectRaw("CONCAT(COALESCE(first_name,''),'-',COALESCE(middle_name,''),'-',COALESCE(last_name,'')) AS full_name")
            , $data)
            ->orderBy('created_at', 'DESC');
    }

    public function pluckUserByRole( string $slug)
    {
        return $this->model
            ->select('users.*')
            ->selectRaw("CONCAT(COALESCE(first_name,''),' ',COALESCE(middle_name,''),' ',COALESCE(last_name,'')) AS full_name")
            ->join('role_user', 'role_user.user_id', '=', 'users.id')
            ->join('roles', 'roles.id', '=', 'role_user.role_id')
            ->where('roles.slug', $slug)
            ->orderBy('users.created_at', 'DESC')
            ->pluck('full_name', 'id');
    }

    public function pluckUserWithEmailByRole( string $slug)
    {
        return $this->model
            ->select('users.*')
            ->selectRaw("CONCAT(COALESCE(first_name,''),' ',COALESCE(middle_name,''),' ',COALESCE(last_name,''),' | ', COALESCE(email,'')) AS user")
            ->join('role_user', 'role_user.user_id', '=', 'users.id')
            ->join('roles', 'roles.id', '=', 'role_user.role_id')
            ->where('roles.slug', $slug)
            ->orderBy('users.created_at', 'DESC')
            ->pluck('user', 'id');
    }

    public function pluckDiscountVoucherCustomer($customerid)
    {
        return $this->model
            ->select('users.*')
            ->selectRaw("CONCAT(COALESCE(first_name,''),' ',COALESCE(middle_name,''),' ',COALESCE(last_name,''),' | ', COALESCE(email,'')) AS user")
            ->join('role_user', 'role_user.user_id', '=', 'users.id')
            ->join('roles', 'roles.id', '=', 'role_user.role_id')
            ->where('roles.slug', 'customer')
            ->where('users.id', $customerid)
            ->orderBy('users.created_at', 'DESC')
            ->pluck('user', 'id');
    }

    /**
     * Find Users by array of IDs
     *
     * @param array $ids
     * @return mixed
     */
    public function find(array $ids)
    {
        return $this->model->select('id', 'first_name', 'middle_name', 'last_name', 'email', 'phone_number')->find($ids);
    }

    /**
     * Get Users having the given role
     *
     * @param $role
     * @return mixed
     */
    public function getUsersHavingRole($role)
    {
        return $this->model->whereHas('roles', function (Builder $query) use ($role) {
            $query->where('roles.slug', $role);
        })->get();
    }

    /**
     * Get Users having verified phone numbers
     *
     * @param $role
     * @return mixed
     */
    public function getPhoneHavingRole($role)
    {
        return $this->model->whereHas('roles', function (Builder $query) use ($role) {
            $query->where('roles.slug', $role);
        })
            ->where([['phone_number', '!=', null], ['phone_is_verified', true]])
            ->get();
    }

    /**
     * Find a user by email.
     *
     * @param $email
     * @return mixed
     */
    public function findByEmail($email)
    {
        return $this->model->where('email', $email)->first();
    }

    public function getLoggedInUser()
    {
        return request()->user();
    }

    /**
     * @param String|null $type
     * @param null $date
     * @return int
     */
    public function getCountByUserType(String $type = null, $date = null)
    {
        $builder = app('db')->table('users')
            ->join('role_user', 'role_user.user_id', '=', 'users.id')
            ->join('roles', 'roles.id', '=', 'role_user.role_id')
            ->where('roles.slug', $type);
        if ($date) {
            $builder->whereDate('users.created_at', $date);
        }
        return $builder->count();
    }

    /**
     * @param $id
     * @return bool
     */
    public function forceDelete($id)
    {
        $user = $this->model->withTrashed()->findOrFail($id);
        $user->forceDelete();
        return true;
    }

    /**
     * @param $id
     * @return bool
     */
    public function restore($id)
    {
        $user = $this->model->onlyTrashed()->findOrFail($id);
        $user->restore();
        return true;
    }

    /**
     * @param $type
     * @param null $role
     * @return mixed
     */
    public function getCountByStatus($role = null)
    {

        $query = $this->model
            ->selectRaw('count(*) as total')
            ->selectRaw("count(case when status = '".Status::ACTIVE_STATUS."' then 1 end) as active")
            ->selectRaw("count(case when status = '".Status::INACTIVE_STATUS."' then 1 end) as inactive");

        if ($role) {
            $query->whereHas('roles', function ($query) use ($role) {
                $query->where('roles.slug', $role);
            });
        }
        $all = $query->first()->toArray();

        return (array) $all;
    }

    public function updateLoggedInUser($model, array $data)
    {
//        $user = $this->model->find(auth()->user()->id);
        return $model->update($data);

    }

    public function getOrderList($userId, string $orderId = null)
    {
        return app('db')
            ->table('orders')
            ->select(
                'users.id', 'orders.id as o_id', 'orders.order_id', 'orders.order_type',
                'orders.quantity', 'orders.amount', 'transactions.status',
                'orders.delivery_status', 'orders.created_at', 'orders.updated_at'
            )
            ->leftJoin('transactions', 'transactions.id', 'orders.transaction_id')
            ->leftJoin('users', 'transactions.user_id', 'users.id')
            ->where('transactions.user_id', $userId)
            ->where(function ($query) use($orderId){
                if ($orderId) {
                    $query->where('orders.order_id', 'like', '%'.$orderId.'%');
                }
            })
            ->orderBy('orders.created_at', 'DESC');
    }

    /**
     * Get the first record matching the $condition or create new one with $data
     *
     * @param array $condition
     * @param array $data
     * @return mixed
     */
    public function firstOrCreate(array $condition, array $data)
    {
        return $this->model->firstOrCreate($condition, $data);
    }

    public function getUserForAPeriod($dailyDate, $startDate, $endDate, $role)
    {
        $query = $this->model
            ->where(function ($query) use ($dailyDate, $startDate, $endDate){
                if($dailyDate)
                    $query->whereDate('created_at', $dailyDate);
                else
                    $query->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->selectRaw('count(id) as total')
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m-%d") as date')
            ->selectRaw("sum(case when is_verified = '".Status::ACTIVE_STATUS."' AND phone_is_verified = '".Status::ACTIVE_STATUS."' then 1 end) as total_user_verified")
            ->selectRaw("sum(case when is_verified = '".Status::INACTIVE_STATUS."' AND phone_is_verified = '".Status::INACTIVE_STATUS."' then 1 end) as total_user_unverified")
            ->selectRaw("sum(case when is_verified = '".Status::ACTIVE_STATUS."' then 1 end) as total_user_verified_with_email")
            ->selectRaw("sum(case when phone_is_verified = '".Status::ACTIVE_STATUS."' then 1 end) as total_user_verified_with_phone");

        if ($role) {
            $query->whereHas('roles', function ($query) use ($role) {
                $query->where('roles.slug', $role);
            });
        }

        return $query->groupBy('date')
            ->orderBy('date', 'desc')
            ->get();
    }

    public function delete($user)
    {
        return $this->model->where('id', $user->id)->delete();
    }

    /**
     * Search User's Full Name and Email
     *
     * @param $search
     * @param $role
     * @return mixed
     */
    public function searchByRole($search, $role)
    {
        return $this->model
            ->select(
                DB::raw("CONCAT(COALESCE(first_name,''), ' ',COALESCE(last_name,''), ' | ', COALESCE(email,'')) AS full_name"),
                'id'
            )
            ->where(function ($query) use ($search){
                $query->whereRaw("CONCAT(COALESCE(first_name,''), ' ', COALESCE(last_name,'')) like ?", ['%' . $search . '%'])
                    ->orWhere('email', 'like', '%' . $search . '%');
            })
            ->whereHas('roles', function ($query) use ($role){
                $query->where('slug', $role);
            })
            ->get('full_name', 'id');
    }

    /**
     * Blacklist User(s)
     *
     * @param array $ids
     * @return mixed
     */
    public function blacklist(array $ids)
    {
        $this->addBlacklistEntries($ids);
        return $this->model->whereIn('id', $ids)->update(['is_blacklisted' => true]);
    }

    /**
     * Blacklist User(s)
     *
     * @param array $ids
     * @return mixed
     */
    public function removeFromBlacklist(array $ids)
    {
        $this->removeBlacklistEntries($ids);
        return $this->model->whereIn('id', $ids)->update(['is_blacklisted' => false]);
    }

    /**
     * Add Black List Entries
     *
     * @param array $ids
     * @return mixed
     */
    private function addBlacklistEntries(array $ids)
    {
        $users = $this->model
            ->select('id', 'first_name', 'middle_name', 'last_name', 'email', 'phone_number')
            ->whereIn('id', $ids)
            ->where('is_blacklisted', false)
            ->get();

        $list = [];
        foreach ($users as $user){
            array_push($list, [
                'name' => $user->getFullName(),
                'email' => $user->email,
                'phone' => $user->phone_number,
                'user_id' => $user->id,
                'listed_by' => auth()->id(),
                'created_at' => now(),
                'updated_at' => now(),
                'remarks' => 'User Blacklisted',
            ]);
        }
        return app('db')->table('users_blacklist')->insert($list);
    }

    /**
     * Add Black List Entries
     *
     * @param array $ids
     * @return mixed
     */
    private function removeBlacklistEntries(array $ids)
    {
        return app('db')->table('users_blacklist')->whereIn('id', $ids)->delete();
    }


    public function changePassword($newPassword)
    {
        return auth()
            ->user()
            ->update([
                'password' => bcrypt($newPassword),
            ]);
    }

    public function updateTwoFA($id, $enable = false)
    {
        return $this->model
            ->where('id', $id)
            ->update([
                'two_fa_enabled' => $enable,
            ]);
    }

    /**
     * Assign Kunyo Currency to User
     *
     * @param $userId
     * @param $quantity
     * @return bool
     */
    public function assignCurrency($userId, $quantity)
    {
        return app('db')->table('users')->where('id', $userId)->increment('kunyo_currency', $quantity);
    }

    /**
     * Count Kunyo Currency Balance of User(s)
     *
     * @param null $userId
     * @return int|mixed
     */
    public function countCurrency($userId = null)
    {
        $query = $this->model->newQuery();
        if ($userId)
            return $query->where('id', $userId)->first()->value('kunyo_currency');

        return $query->sum('kunyo_currency');

    }
}
