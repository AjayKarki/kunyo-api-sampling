<?php

namespace Foundation\Services;

use App\Foundation\Lib\History;
use Foundation\Builders\Filters\GiftCards\Filter;
use Foundation\Lib\Meta;
use Foundation\Models\GiftCardsCode;
use Illuminate\Support\Arr;
use Foundation\Models\GiftCard;
use Neputer\Config\Status;
use Neputer\Supports\BaseService;

/**
 * Class GiftCardService
 * @package Foundation\Services
 */
class GiftCardService extends BaseService
{

    /**
     * The GiftCard instance
     *
     * @var $model
     */
    protected $model;
    /**
     * @var GiftCardsCode
     */
    private $giftCardsCode;

    /**
     * GiftCardService constructor.
     * @param GiftCard $giftCard
     * @param GiftCardsCode $giftCardsCode
     */
    public function __construct(GiftCard $giftCard, GiftCardsCode $giftCardsCode)
    {
        $this->model = $giftCard;
        $this->giftCardsCode = $giftCardsCode;
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
            $this->model
                ->select('gift_cards.*', 'categories.category_name')
                ->leftJoin('categories', 'categories.id', '=', 'category_id')
                ->addSelect([
                    'category_tree' => app('db')
                        ->table('categories as parent')
                        ->selectRaw('CONCAT(category_name, " ~ ",categories.category_name) AS parent_name')
                        ->whereColumn('parent.id', 'categories.parent_id')
                ]), $data)
            ->with('active_codes:id,is_used,gift_cards_id')
            // ->withCount('active_codes')
            ->latest();
    }

    public function insertCodes(array $data, $giftCardsId)
    {
        $code = app('db')
            ->table('gift_cards_codes')
            ->where('id', Arr::get($data, 'id', -1))
            ->where('gift_cards_id', $giftCardsId)
            ->first();
        if ($code) {
            app('db')
                ->table('gift_cards_codes')
                ->where('id', Arr::get($data, 'id', -1))
                ->where('gift_cards_id', $giftCardsId)
                ->update([
                    'codes' => Arr::get($data, 'codes'),
                    'gift_cards_id' => $giftCardsId,
                    'updated_at' => now(),
                ]);


            if($code->codes != Arr::get($data, 'codes')){
                app(HistoryService::class)->create(null, [
                    'title' => 'Code Updated',
                    'information' => 'Code is Updated from: ' . url()->previous(),
                    'user_id' => auth()->id(),
                    'user_name' => auth()->user()->getFullName(),
                    'type' => History::TYPE_UPDATE,
                    'old_value' => $code->codes,
                    'new_value' => Arr::get($data, 'codes'),
                    'historyable_id' => $code->id,
                    'historyable_type' => GiftCardsCode::class
                ]);
            }
        } else {
            app('db')
                ->table('gift_cards_codes')
                ->insert([
                    'codes' => Arr::get($data, 'codes'),
                    'gift_cards_id' => $giftCardsId,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]);
        }
    }

    public function insertCodesByIdAndCodeForOrder($codeId, $giftCardsId)
    {
        $code = app('db')
            ->table('gift_cards_codes')
            ->where(function ($sql) use ($codeId) {
                $sql->orWhere('id', $codeId);
                $sql->orWhere('codes', $codeId);
            })
            /*->where('id', $codeId)
            ->orWhere('codes', $codeId)*/
            ->where('gift_cards_id', $giftCardsId)
            ->first();
        if ($code) {
            app('db')
                ->table('gift_cards_codes')
                ->where('id', $code->id)
                ->where('gift_cards_id', $giftCardsId)
                ->update([
                    'codes' => $code->codes,
                    'gift_cards_id' => $giftCardsId,
                    'updated_at' => now(),
                ]);
        } else {
            app('db')
                ->table('gift_cards_codes')
                ->insert([
                    'codes' => $codeId,
                    'gift_cards_id' => $giftCardsId,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]);
        }
        return app('db')
            ->table('gift_cards_codes')
            ->where(function ($sql) use ($codeId) {
                $sql->orWhere('id', $codeId);
                $sql->orWhere('codes', $codeId);
            })
           /* ->where('id', $codeId)
            ->orWhere('codes', $codeId)*/
            ->where('gift_cards_id', $giftCardsId)
            ->first();
    }

    public function deleteCodes(array $ids, $giftCardsId)
    {
        app('db')
            ->table('gift_cards_codes')
            ->where('gift_cards_id', $giftCardsId)
            ->where('is_used', 0)
            ->whereNotIn('id', $ids)
            ->delete();
    }

    public function getLatest($limit = 8, $keyword = null, $category = null)
    {
        return $this->model
            ->newQuery()
            ->whereHas('category', function ($query) {
                $query->where('categories.status', Status::ACTIVE_STATUS);
            })
            ->where(function ($query) use ($keyword) {
                if ($keyword) {
                    $query->where('name', 'like', '%' . $keyword . '%');
                }
            })
            ->when($category, function ($query) use ($category) {
                $query->where('category_id', $category);
            })
            ->limit($limit)
            ->where('status', Status::ACTIVE_STATUS)
            ->where('is_order_disable', Status::INACTIVE_STATUS)
            ->whereIntegerNotInRaw('id', explode(',', Meta::get('gift_card_id_test_purpose')) ?? [])
            ->withCount('active_codes')
            ->orderBy('created_at', 'DESC')
            ->get();
    }

    public function bySlug(string $slug)
    {
        return $this->model
            ->select(
                'gift_cards.*',
                'publishers.name as publisher_name',
                'developers.name as developer_name',
                'genres.name as genre_name',
                'platforms.name as platform_name',
                'delivery_modes.name as delivery_mode_name',
                'delivery_times.name as delivery_time_name',
                'regions.name as region_name'
            )
            ->with('category.parent')
            ->leftJoin('publishers', 'publishers.id', '=', 'gift_cards.publisher_id')
            ->leftJoin('developers', 'developers.id', '=', 'gift_cards.developer_id')
            ->leftJoin('genres', 'genres.id', '=', 'gift_cards.genre_id')
            ->leftJoin('platforms', 'platforms.id', '=', 'gift_cards.platform_id')
            ->leftJoin('delivery_modes', 'delivery_modes.id', '=', 'gift_cards.delivery_mode_id')
            ->leftJoin('delivery_times', 'delivery_times.id', '=', 'gift_cards.delivery_time_id')
            ->leftJoin('regions', 'regions.id', '=', 'gift_cards.region_id')
            ->where('gift_cards.slug', $slug)
//            ->where('gift_cards.status', Status::ACTIVE_STATUS)
//            ->where('gift_cards.is_order_disable', Status::INACTIVE_STATUS)
            ->whereHas('category', function ($query) {
                return $query->where('categories.status', Status::ACTIVE_STATUS);
            })
            ->with('active_codes')
            ->first();
    }

    public function getDetail($giftCardId)
    {
        return $this->model
            ->where('id', $giftCardId)
            ->first();
    }

    public function getInActiveCodes($giftCardId)
    {
        return $this->model
            ->select( 'code.codes', 'code.id' )
            ->where('gift_cards.id', $giftCardId)
            ->join('gift_cards_codes as code', 'code.gift_cards_id', '=', 'gift_cards.id')
            ->where('code.is_used', 0)
            ->whereNull('code.deleted_at')
            ->whereNull('code.deleted_by')
            ->pluck('codes', 'id');
    }

    public function getGiftCardCode($orderId)
    {
        return app('db')
            ->table('gift_cards_codes')
            ->leftJoin(
                'orders_gift_cards_codes as ordered_code',
                'ordered_code.gift_cards_codes_id',
                '=',
                'gift_cards_codes.id'
            )
            ->where('ordered_code.order_id', $orderId)
            ->get();
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
     * Count Gift Cards with no original price
     *
     * @return mixed
     */
    public function getCountByPrice()
    {
        return app('db')
            ->table('gift_cards')
            ->selectRaw('count(*) as total')
            ->selectRaw("count(case when price = original_price OR original_price = 0 then 1 end) as no_original")
            ->first();
    }

    /**
     * Get List of Gift Cards having no Original Price or Same Original Price as Selling Price
     *
     */
    public function getWithNoPrice()
    {
        return $this->model
            ->whereColumn('price', 'original_price')
            ->orWhere('original_price', 0)
            ->get();
    }

    public function getCode($id)
    {
        return (array) app('db')
            ->table('gift_cards_codes')
            ->where('id', $id)
            ->first();
    }

    public function items($limit)
    {
        return $this->model->query()
            ->select('gift_cards.*',
                'publishers.name as publisher_name',
                'developers.name as developer_name',
                'genres.name as genre_name',
                'platforms.name as platform_name',
                'delivery_modes.name as delivery_mode_name',
                'delivery_times.name as delivery_time_name')
            ->leftJoin('publishers', 'publishers.id', '=', 'gift_cards.publisher_id')
            ->leftJoin('developers', 'developers.id', '=', 'gift_cards.developer_id')
            ->leftJoin('genres', 'genres.id', '=', 'gift_cards.genre_id')
            ->leftJoin('platforms', 'platforms.id', '=', 'gift_cards.platform_id')
            ->leftJoin('delivery_modes', 'delivery_modes.id', '=', 'gift_cards.delivery_mode_id')
            ->leftJoin('delivery_times', 'delivery_times.id', '=', 'gift_cards.delivery_time_id')
            ->with('category:category_name,id,slug')
            ->limit($limit)
            ->latest()
            ->get();
    }

    public function byId($id)
    {
        return $this->model
            ->select('*')
            ->selectRaw("JSON_UNQUOTE(JSON_EXTRACT(metas, '$.discount.imepay')) as imepay_discount")
            ->selectRaw("JSON_UNQUOTE(JSON_EXTRACT(metas, '$.discount.khalti')) as khalti_discount")
            ->selectRaw("JSON_UNQUOTE(JSON_EXTRACT(metas, '$.discount.prabhupay')) as prabhupay_discount")
            ->selectRaw("JSON_UNQUOTE(JSON_EXTRACT(metas, '$.discount.nicasia')) as nicasia_discount")
            ->selectRaw("JSON_UNQUOTE(JSON_EXTRACT(metas, '$.discount.esewa')) as esewa_discount")
            ->where('status', Status::ACTIVE_STATUS)
            ->where('is_order_disable', Status::INACTIVE_STATUS)
            ->where('id', $id)
            ->whereHas('category', function ($query) {
                return $query->where('categories.status', Status::ACTIVE_STATUS);
            })
            ->first();
    }

    public function withPricing($filter = [])
    {
        return $this->model
            ->select([
                'gift_cards.id',
                'gift_cards.id AS gift_card_id',
                'gift_cards.name AS gift_card_name',
                'gift_cards.price AS gift_card_selling_price',
                'gift_cards.original_price AS gift_card_original_price',
            ])
            ->with('prices.region')
            ->latest();
    }

}
