<?php

namespace Foundation\Services;

use App\Foundation\Lib\Referral as ReferralLib;
use Foundation\Models\Referral;
use Foundation\Models\ReferralList;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Neputer\Supports\BaseService;

/**
 * Class ReferralService
 * @package Foundation\Services
 */
class ReferralService extends BaseService
{

    /**
     * The Referral instance
     *
     * @var $model
     */
    protected $model;

    /**
     * @var ReferralList
     */
    private $referralList;

    /**
     * ReferralService constructor.
     * @param Referral $referral
     * @param ReferralList $referralList
     */
    public function __construct(Referral $referral, ReferralList $referralList)
    {
        $this->model = $referral;
        $this->referralList = $referralList;
    }

    /**
     * Filter
     *
     * @param array $data
     * @return mixed
     */
    public function filter(array $data = [])
    {
        $query = $this->model->newQuery();

        if($search = Arr::get($data, 'search.value')){
            $query->whereHas('creator', function ($query) use ($search){
                $query->where('first_name', 'like', '%' . $search . '%')
                    ->orWhere('last_name', 'like', '%' . $search . '%');
            });

            $query->orWhere('link', 'like', '%' . $search . '%');
        }

        if ($user = Arr::get($data, 'filter.name')){
            $query->where('user_name', 'like', '%' . $user . '%');
        }

        if ($link = Arr::get($data, 'filter.link')){
            $query->where('user_name', 'like', '%' . $link . '%');
        }

        if ($count = Arr::get($data, 'filter.compare_count')){
            $operator = Arr::get($data, 'filter.compare_operator');
            $onlyVerified = (bool) Arr::get($data, 'filter.compare_only_verified');
            $query->whereHas('referralList', function (Builder $query) use ($onlyVerified) {
                $query->where('status', $onlyVerified);
            }, $operator, $count);
        }

        if ($createdFrom = Arr::get($data, 'filter.created.from_date')) {
            $query->whereDate('created_at', '>=', $createdFrom);
        }

        if ($createdTo = Arr::get($data, 'filter.created.to_date')) {
            $query->whereDate('created_at', '<=', $createdTo);
        }


        return  $query->withCount('referralList')->with('creator:id,first_name,last_name')->latest();
    }

    /**
     * Filter referral list
     *
     * @param $referralId
     * @param array $data
     * @return Builder
     */
    public function filterList($referralId, array $data = [])
    {
        $query = $this->referralList->newQuery();
        $query->withCount(['orders as amount_spent' => function ($query){
                $query->select(DB::raw('sum(amount * quantity - discounted_amount)'));
        }]);
        if($name = Arr::get($data, 'search.value')){
            $query->whereHas('user', function ($query) use ($name){
                $query->where('first_name', 'like', '%' . $name . '%')
                    ->orWhere('last_name', 'like', '%' . $name . '%');
            });
        }

        return $query->where('referral_id', $referralId)->latest();
    }

    /**
     * Return first model that matches the condition
     *
     * @param $conditions
     * @return mixed
     */
    public function firstWhere($conditions)
    {
        return $this->model->where($conditions)->first();
    }

    /**
     * Return models that match the conditions
     *
     * @param $conditions
     * @return mixed
     */
    public function getWhere($conditions)
    {
        return $this->model->where($conditions)->get();
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function newReferralList($data)
    {
        return $this->referralList->create($data);
    }

    /**
     * Count Number of Referral Participant
     *
     * @return mixed
     */
    public function count()
    {
        return $this->model->count();
    }

    /**
     * Count Number of Referral List
     *
     * @param null $referralId
     * @return Builder|\Illuminate\Database\Eloquent\Model|object
     */
    public function countList($referralId = null)
    {
        $query = $this->referralList->newQuery();
        $query->selectRaw('count(*) as total')
            ->selectRaw("count(case when status = '" . ReferralLib::STATUS_VERIFIED . "' then 1 end) as verified")
            ->selectRaw("count(case when status = '" . ReferralLib::STATUS_UNVERIFIED . "' then 1 end) as unverified");

        if ($referralId)
            $query->where('referral_id', $referralId);

        return $query->first();
    }

    /**
     * Get Referral List
     *
     * @param $conditions
     * @return mixed
     */
    public function getListWhere($conditions)
    {
        return $this->referralList
            ->where($conditions)
            ->withCount(['orders as amount_spent' => function ($query){
                $query->select(DB::raw('sum(amount * quantity - discounted_amount)'));
            }])
            ->get();
    }

    /**
     * Mark Verified referrals as Settled
     *
     * @param $where
     * @param $data
     * @return mixed
     */
    public function updateList($where, $data)
    {
        return $this->referralList
            ->where($where)
            ->update($data);
    }

}
