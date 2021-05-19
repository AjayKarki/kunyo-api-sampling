<?php

namespace Foundation\Services;

use Foundation\Builders\Filters\TopUp\Filter;
use Foundation\Models\TopUpAmount;
use Foundation\Models\User;
use Foundation\Resolvers\RevisionResolver;
use Illuminate\Support\Arr;
use Foundation\Models\TopUp;
use Neputer\Config\Status;
use Neputer\Supports\BaseService;
use function foo\func;

/**
 * Class TopUpService
 * @package Foundation\Services
 */
class TopUpService extends BaseService
{

    /**
     * The TopUp instance
     *
     * @var $model
     */
    protected $model;
    /**
     * @var TopUpAmount
     */
    private TopUpAmount $amount;

    /**
     * TopUpService constructor.
     * @param TopUp $topUp
     * @param TopUpAmount $amount
     */
    public function __construct(TopUp $topUp, TopUpAmount $amount)
    {
        $this->model = $topUp;
        $this->amount = $amount;
    }

    /**
     * Filter
     *
     * @param array $data
     * @return mixed
     */
    public function filter(array $data)
    {
        return Filter::apply(
            $this->model->with('amounts')
                ->select('game_top_ups.*', 'categories.category_name')
                ->leftJoin('categories', 'categories.id', '=', 'category_id')
                ->addSelect([
                    'category_tree' => app('db')
                        ->table('categories as parent')
                        ->selectRaw('CONCAT(category_name, " ~ ",categories.category_name) AS parent_name')
                        ->whereColumn('parent.id', 'categories.parent_id')
                ]), $data)
            ->withCount('amounts')
            ->orderBy('game_top_ups.created_at', 'DESC');

//            ->withCount([
//                'amounts' => function ($query) {
//                    $query->where('game_top_ups_amounts.status', 0);
//                }
//            ]);

    }

    /**
     * Get Top Up having given ID with Amounts
     *
     * @param $id
     * @param $amountId
     * @return TopUp|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model
     */
    public function getById($id, $amountId)
    {
        return $this->model
            ->with(['amounts' => function ($query) use ($amountId){
                $query->where('id', $amountId);
            }])
            ->find($id);
    }

    public function insertAmounts(array $data, $topUpId)
    {
        $amount = app('db')
            ->table('game_top_ups_amounts')
            ->where('title', Arr::get($data, 'title'))
            ->orWhere('id', Arr::get($data, 'id'))
            ->where('game_top_ups_id', $topUpId)
            ->first();

        if ($amount) {
            app('db')
                ->table('game_top_ups_amounts')
                ->where('title', Arr::get($data, 'title'))
                ->orWhere('id', Arr::get($data, 'id'))
                ->where('game_top_ups_id', $topUpId)
                ->update([
                    'title' => Arr::get($data, 'title'),
                    'price' => Arr::get($data, 'price'),
                    'original_price' => Arr::get($data, 'original_price'),
                    'game_top_ups_id' => $topUpId,
                    'status' => 0,
                    'updated_at' => now(),
                ]);

            RevisionResolver::topUpAmountUpdated($topUpId, [
                'user_id' => auth()->id(),
                'meta' => [
                    'new'     => [
                        'id'    => $amount->id,
                        'title' => Arr::get($data, 'title'),
                        'price' => Arr::get($data, 'price'),
                        'original_price' => Arr::get($data, 'original_price'),
                    ],
                    'old'     => [
                        'id'    => $amount->id,
                        'title' => Arr::get($data, 'title'),
                        'price' => Arr::get($data, 'price'),
                        'original_price' => Arr::get($data, 'original_price'),
                    ],
                ]
            ]);
        } else {
            $topUpAmountId = app('db')
                ->table('game_top_ups_amounts')
                ->insertGetId([
                    'title' => Arr::get($data, 'title'),
                    'price' => Arr::get($data, 'price'),
                    'original_price' => Arr::get($data, 'original_price'),
                    'game_top_ups_id' => $topUpId,
                    'status' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

            RevisionResolver::topUpAmountCreated($topUpId, [
                'user_id' => auth()->id(),
                'meta'    => [
                    'new'     => [
                        'id'    => $topUpAmountId,
                        'title' => Arr::get($data, 'title'),
                        'price' => Arr::get($data, 'price'),
                        'original_price' => Arr::get($data, 'original_price'),
                    ]
                ]
            ]);
        }
    }

    public function deleteAmounts(array $ids, $topUpId)
    {
        app('db')
            ->table('game_top_ups_amounts')
            ->where('game_top_ups_id', $topUpId)
            ->whereNotIn('id', $ids)
            ->delete();
    }

    public function deleteAttributes(array $ids, $topUpId)
    {
        app('db')
            ->table('game_top_ups_attributes')
            ->where('game_top_ups_id', $topUpId)
            ->whereNotIn('id', $ids)
            ->delete();
    }

    public function insertAttributes(array $data, $topUpId)
    {
        if ($id = Arr::get($data, 'id')) {
            $attributeBuilder = app('db')
                ->table('game_top_ups_attributes')
                ->where('id', $id)
                ->where('game_top_ups_id', $topUpId);

            $attribute = $attributeBuilder->first();

            if ($attribute) {
                $attributeBuilder
                    ->update([
                        'title' => Arr::get($data, 'title'),
                        'placeholder' => Arr::get($data, 'placeholder'),
                        'game_top_ups_id' => $topUpId,
                        'updated_at' => now(),
                        'status' => 1,
                        'required' => Arr::get($data, 'required'),
                    ]);
            }
        } else {
            app('db')
                ->table('game_top_ups_attributes')
                ->insert([
                    'title' => Arr::get($data, 'title'),
                    'placeholder' => Arr::get($data, 'placeholder'),
                    'game_top_ups_id' => $topUpId,
                    'created_at' => now(),
                    'updated_at' => now(),
                    'status' => 1,
                    'required' => Arr::get($data, 'required'),
                ]);
        }
    }

    public function bySlug(string $slug)
    {
        return $this->model
            ->select(
                'game_top_ups.*',
                'publishers.name as publisher_name',
                'developers.name as developer_name',
                'genres.name as genre_name',
                'platforms.name as platform_name',
                'delivery_modes.name as delivery_mode_name',
                'delivery_times.name as delivery_time_name',
                'regions.name as region_name'
            )
            ->with('category.parent')
            ->with('attributes')
            ->leftJoin('publishers', 'publishers.id', '=', 'game_top_ups.publisher_id')
            ->leftJoin('developers', 'developers.id', '=', 'game_top_ups.developer_id')
            ->leftJoin('genres', 'genres.id', '=', 'game_top_ups.genre_id')
            ->leftJoin('platforms', 'platforms.id', '=', 'game_top_ups.platform_id')
            ->leftJoin('delivery_modes', 'delivery_modes.id', '=', 'game_top_ups.delivery_mode_id')
            ->leftJoin('delivery_times', 'delivery_times.id', '=', 'game_top_ups.delivery_time_id')
            ->leftJoin('regions', 'regions.id', '=', 'game_top_ups.region_id')
            ->with([
                'amounts' => function ($query) {
                    $query->where('game_top_ups_amounts.status', 0);
                }
            ])
            ->where('game_top_ups.slug', $slug)
//            ->where('game_top_ups.status', Status::ACTIVE_STATUS)
//            ->where('game_top_ups.is_order_disable', Status::INACTIVE_STATUS)
            ->whereHas('category', function ($query) {
                return $query->where('categories.status', Status::ACTIVE_STATUS);
            })
            ->first();
    }

    public function getLatest($limit = 8, $keyword = null, $category = null)
    {
        return $this->model
            ->select('game_top_ups.*')
            ->with('amounts')
//            ->has('amounts', '>', 0)
            ->whereHas('category', function ($query) {
                $query->where('categories.status', Status::ACTIVE_STATUS);
            })
            ->selectSub(app('db')
                ->table('game_top_ups_amounts')
                ->selectRaw('MAX(game_top_ups_amounts.price) AS max_amount')
                ->whereColumn('game_top_ups_amounts.game_top_ups_id', 'game_top_ups.id')
                ->limit(1)
                ->toSql(), 'max_top_up_amount')
            ->selectSub(app('db')
                ->table('game_top_ups_amounts')
                ->selectRaw('MIN(game_top_ups_amounts.price) AS max_amount')
                ->whereColumn('game_top_ups_amounts.game_top_ups_id', 'game_top_ups.id')
                ->limit(1)
                ->toSql(), 'min_top_up_amount')
            ->where(function ($query) use ($keyword) {
                if ($keyword) {
                    $query->where('game_top_ups.name', 'like', '%' . $keyword . '%');
                }
            })
            ->when($category, function ($query) use ($category) {
                $query->where('game_top_ups.category_id', $category);
            })
            ->limit($limit)
            ->where('status', Status::ACTIVE_STATUS)
            ->where('is_order_disable', Status::INACTIVE_STATUS)
            ->latest()
            ->get();
    }

    public function getDetail($topUpAmountId)
    {
        return app('db')
            ->table('game_top_ups_amounts')
            ->select('game_top_ups_amounts.*', 'top_ups.name as top_ups_name', 'top_ups.image', 'top_ups.slug as top_ups_slug', 'top_ups.description as description')
            ->selectRaw('concat_ws("  -  ",top_ups.name,game_top_ups_amounts.title) as group_title')
            ->leftJoin('game_top_ups as top_ups', 'top_ups.id', '=', 'game_top_ups_amounts.game_top_ups_id')
            ->where('game_top_ups_amounts.id', $topUpAmountId)
            ->first();
    }

    public function getPlayerMetas($topUpId)
    {

    }

    public function filterTopUpAmounts($id, string $name = null)
    {
        return $this->amount
            ->where(function ($query) use ($name) {
                if ($name) {
                    $query->where('game_top_ups_amounts.title', 'like', '%' . $name . '%')
                        ->orWhere('game_top_ups_amounts.price', 'like', '%' . $name . '%');
                }
            })
            ->where('game_top_ups_id', $id)
            ->with('prices.region')
            ->latest();
    }

    public function statusByType()
    {
        $dataAll = $this->model
            ->selectRaw('
                    IF((status = ' . Status::ACTIVE_STATUS . '), COUNT(*), "0") as active,
                    IF((status = ' . Status::INACTIVE_STATUS . '), COUNT(*), "0") as inactive,
                    IF((status = ' . Status::INACTIVE_STATUS . ' || status = ' . Status::ACTIVE_STATUS . '), COUNT(*), "0") as Total
             ')
            ->groupBy('status')
            ->get();
        $data['active'] = $dataAll->sum('active');
        $data['inactive'] = $dataAll->sum('inactive');
        $data['total'] = $data['active'] + $data['inactive'];
        return $data;
    }

    public function filterAttributes($top_up_id, string $name = null)
    {
        return app('db')
            ->table('game_top_ups_attributes')
            ->select('game_top_ups_attributes.*')
            ->where(function ($query) use ($name) {
                if ($name) {
                    $query->where('game_top_ups_attributes.title', 'like', '%' . $name . '%')
                        ->orWhere('game_top_ups_attributes.placeholder', 'like', '%' . $name . '%');
                }
            })
            ->where('game_top_ups_id', $top_up_id)
            ->get();
    }

    public function getTopUpAmount($topUpAmountId)
    {
        return app('db')
            ->table('game_top_ups_amounts')
            ->select('game_top_ups_amounts.*', 'top_up.name as top_up_name', 'top_up.id as top_up_id')
            ->leftJoin('game_top_ups as top_up', 'top_up.id', '=', 'game_top_ups_amounts.game_top_ups_id')
            ->where('game_top_ups_amounts.id', $topUpAmountId)
            ->first();
    }

    public function getTopUpAmountCode($orderId)
    {
        return app('db')
            ->table('orders_top_ups_amounts')
            ->where('orders_top_ups_amounts.order_id', $orderId)
            ->get();
    }

    public function getAttributesByAmountId($amountId)
    {
        return app('db')
            ->table('game_top_ups_amounts')
            ->select('game_top_ups_attributes.*')
            ->leftJoin (
                'game_top_ups as top_up',
                'top_up.id',
                '=',
                'game_top_ups_amounts.game_top_ups_id'
            )
            ->leftJoin(
                'game_top_ups_attributes',
                'game_top_ups_attributes.game_top_ups_id',
                '=',
                'top_up.id'
            )
            ->where('game_top_ups_amounts.id', $amountId)
            ->get();
    }

    public function isActive($slug)
    {
        return $this->model
            ->where('status', Status::ACTIVE_STATUS)
            ->whereHas('category', function ($query) {
                return $query->where('categories.status', Status::ACTIVE_STATUS);
            })
            ->where('slug', $slug)
            ->exists();
    }

    /**
     * Get Top Up Amount by ID
     *
     * @param $amountId
     * @return mixed
     */
    public function getAmountById($amountId)
    {
        return app('db')
            ->table('game_top_ups_amounts')
            ->find($amountId);
    }

    /**
     * Get Count of TopUps having no Original Price or Same Original Price as Selling Price
     *
     * @return object
     */
    public function getCountByPrice()
    {
        $result = app('db')
            ->table('game_top_ups_amounts')
            ->selectRaw('count(*) as total')
            ->addSelect('game_top_ups_id')
            ->selectRaw("count(case when price = original_price OR original_price = 0 then 1 end) as no_original")
            ->groupBy('game_top_ups_id')
            ->get();

        $data['no_original'] = $result->where('no_original', '>', 0)->count();
        $data['total'] = $result->count();
        return (object) $data;
    }

    /**
     * Get List of TopUps having no Original Price or Same Original Price as Selling Price
     *
     */
    public function getWithNoPrice()
    {
        return $this->model
            ->whereHas('amounts', function($query){
                $query->whereColumn('price', 'original_price')
                    ->orWhere('original_price', 0);
            })
            ->get();
    }

    public function items($limit)
    {
        return $this->model->query()
            ->select('game_top_ups.*')
            ->selectSub(app('db')
                ->table('game_top_ups_amounts')
                ->selectRaw('MAX(game_top_ups_amounts.price) AS max_amount')
                ->whereColumn('game_top_ups_amounts.game_top_ups_id', 'game_top_ups.id')
                ->limit(1)
                ->toSql(), 'max_top_up_amount')
            ->selectSub(app('db')
                ->table('game_top_ups_amounts')
                ->selectRaw('MIN(game_top_ups_amounts.price) AS max_amount')
                ->whereColumn('game_top_ups_amounts.game_top_ups_id', 'game_top_ups.id')
                ->limit(1)
                ->toSql(), 'min_top_up_amount')
            ->limit($limit)
            ->latest()
            ->get();
    }

    public function updateAmountPrice($id, $original, $selling)
    {
        return $this->amount
            ->where('id', $id)
            ->update([
                'original_price' => $original,
                'price' => $selling
            ]);
    }

    public function withPricing($filter = [])
    {
        return $this->amount
            ->select([
                'game_top_ups_amounts.id',
                'game_top_ups_amounts.id AS amount_id',
                'game_top_ups_amounts.title AS amount_title',
                'game_top_ups.name AS top_up_name',
                'game_top_ups.id AS top_up_id',
            ])
            ->leftJoin('game_top_ups', 'game_top_ups.id', '=', 'game_top_ups_amounts.game_top_ups_id')
            ->with('prices.region')
            ->where('game_top_ups.id', Arr::get($filter, 'filter.topUp'))
            ->orderBy('game_top_ups_amounts.created_at', 'DESC');
    }

}
