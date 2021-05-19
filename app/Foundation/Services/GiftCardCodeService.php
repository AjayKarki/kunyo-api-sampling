<?php

namespace Foundation\Services;

use Foundation\Builders\Filters\GiftCardCodes\Filter;
use Foundation\Models\GiftCardsCode;
use Neputer\Config\Status;
use Neputer\Supports\BaseService;

/**
 * Class GiftCardService
 * @package Foundation\Services
 */
class GiftCardCodeService extends BaseService
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
     * @param GiftCardsCode $giftCardsCode
     */
    public function __construct(GiftCardsCode $giftCardsCode)
    {
        $this->model = $giftCardsCode;
    }

    /**
     * Filter
     *
     * @param array $data
     * @param $id
     * @return mixed
     */
    public function filter(array $data, $id = null)
    {
        $query = $this->model 
            ->select(
                'gift_cards_codes.*',
                'gift_cards.name as gift_card_name',
                'orders_gift_cards_codes.id as orders_gift_cards_codes_id', 'orders_gift_cards_codes.order_id',
                'orders.id as orders_id', 'orders.transaction_id as trans_id',
                'transactions.transaction_id as transaction_id', 'transactions.picked_by as picker_id', 'transactions.user_id as buyer_id', 'transactions.created_at as transaction_date', 'transactions.status as transaction_status'
            )
            ->selectRaw('CONCAT_WS(" ", buyer.first_name, buyer.middle_name, buyer.last_name) AS buyer_name')
            ->selectRaw('CONCAT_WS(" ", picker.first_name, picker.middle_name, picker.last_name) AS picker_name')
            ->where(function ($query) use ($id){
                if($id){
                    $query->where('gift_cards_id', $id);
                }
            })
            ->leftJoin('gift_cards', 'gift_cards_codes.gift_cards_id', '=', 'gift_cards.id')
            ->leftJoin('orders_gift_cards_codes', 'gift_cards_codes.id', '=', 'orders_gift_cards_codes.gift_cards_codes_id')
            ->leftJoin('orders', 'orders_gift_cards_codes.order_id', '=', 'orders.id')
            ->leftJoin('transactions', 'orders.transaction_id', '=', 'transactions.id')
            ->leftJoin('users as buyer', 'buyer.id', '=', 'transactions.user_id')
            ->leftJoin('users as picker', 'picker.id', '=', 'transactions.picked_by')
            ->latest('gift_cards_codes.created_at');

        return Filter::apply($query, $data);
    }

    /**
     * Get the Code Value for given ID
     *
     * @param $id
     * @return mixed
     */
    public function getCode($id)
    {
        return app('db')
            ->table('gift_cards_codes')
            ->where('id', $id)
            ->first();
    }

    /**
     * Get Code ID from Code Value
     *
     * @param $codes
     * @return mixed
     */
    public function getCodeIdFromValue($codes)
    {
        return $this->model->whereIn('codes', $codes)->pluck('id');
    }

    /**
     * Delete Multiple Codes
     *
     * @param array $ids
     * @param $giftCardsId
     * @return mixed
     */
    public function destroy(array $ids, $giftCardsId)
    {
        return $this->model
            ->where('gift_cards_id', $giftCardsId)
            ->where('is_used', 0)
            ->whereNotIn('id', $ids)
            ->delete();
    }

    public function usedStatus($giftCardId = null)
    {
        return $this->model
            ->selectRaw('count(*) as total')
            ->selectRaw("count(case when is_used = '".Status::ACTIVE_STATUS."' then 1 end) as used")
            ->selectRaw("count(case when is_used = '".Status::INACTIVE_STATUS."' then 1 end) as unused")
            ->where(function ($query) use ($giftCardId){
                if($giftCardId)
                    $query->where('gift_cards_id', $giftCardId);
            })
            ->first()->toArray();
    }

    /**
     *
     * @param $id
     *
     * @return GiftCardsCode | null
     */
    public function findOrFail($id)
    {
        return $this->model->withTrashed()->findOrFail($id);
    }

}
