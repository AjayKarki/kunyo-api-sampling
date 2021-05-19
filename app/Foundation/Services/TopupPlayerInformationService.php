<?php

namespace Foundation\Services;

use App\Foundation\Lib\TopupPlayerInformation as AttributeInfo;
use App\Foundation\Lib\TopupPlayerInformation as TopupInfo;
use Foundation\Models\TopupPlayerInformation;
use Neputer\Supports\BaseService;

/**
 * Class TopupPlayerInformationService
 * @package Foundation\Services
 */
class TopupPlayerInformationService extends BaseService
{

    /**
     * The TopupPlayerInformation instance
     *
     * @var $model
     */
    protected $model;

    /**
     * TopupPlayerInformationService constructor.
     * @param TopupPlayerInformation $topupPlayerInformation
     */
    public function __construct(TopupPlayerInformation $topupPlayerInformation)
    {
        $this->model = $topupPlayerInformation;
    }

    /**
     * Check if there are any Information request not acknowledged by customer
     *
     * @param $orderId
     * @return bool
     */
    public function hasPendingRequest($orderId)
    {
        $count = $this->model->where('order_id', $orderId)->where('status', AttributeInfo::STATUS_REQUESTED)->count();
        return $count > 0;
    }

    /**
     * Bulk Insert Data
     *
     * @param $data
     * @return mixed
     */
    public function insert($data)
    {
        return $this->model->insert($data);
    }

    /**
     * Get List of Attributes which must be resubmitted
     *
     * @param $orderId
     * @param $userId
     * @return TopupPlayerInformation
     */
    public function getResubmitAttributes($orderId, $userId)
    {
        return $this->model->select('topup_player_information.*')
            ->leftJoin('orders', 'topup_player_information.order_id', '=', 'orders.id')
            ->leftJoin('transactions', 'orders.transaction_id', '=', 'transactions.id')
            ->where('transactions.user_id', $userId)
            ->where('topup_player_information.order_id', $orderId)
            ->where('topup_player_information.status', AttributeInfo::STATUS_REQUESTED)
            ->get();
    }

    public function save($data, $remarks)
    {
        foreach ($data as $id => $attribute){
            $this->model->where('id', $id)->update([
                'value' => $attribute['value'],
                'customer_remarks' => $remarks,
                'updated_at' => now()
            ]);
        }

        // Get first element's id and find its batch and update all items having thar batch with submitted status
        $batch = $this->model->find(array_key_first($data))->batch;
        $this->model->where('batch', $batch)->update(['status' => TopupInfo::STATUS_SUBMITTED,]);

        return 1;
    }

    public function getPlayerInfo($status, $userId = null, $transactionId = null)
    {
        return $this->model
            ->select(
                'topup_player_information.id as information_id', 'topup_player_information.order_id as information_order_id', 'topup_player_information.batch',
                'orders.id as order_id', 'orders.transaction_id',
                'game_top_ups_amounts.title as top_up_title',
                'game_top_ups.name as top_up_name',
                'transactions.transaction_id as trans_id'
            )
            ->leftJoin('orders', 'topup_player_information.order_id', '=', 'orders.id')
            ->leftJoin('game_top_ups_amounts', 'orders.order_type_id', '=', 'game_top_ups_amounts.id')
            ->leftJoin('game_top_ups', 'game_top_ups_amounts.game_top_ups_id', '=', 'game_top_ups.id')
            ->leftJoin('transactions', 'orders.transaction_id', '=', 'transactions.id')
            ->where(function ($query) use ($userId, $transactionId){
                if($userId)
                    $query->where('transactions.user_id', $userId);
                if($transactionId)
                    $query->where('transactions.id', $transactionId);
            })
            ->where('topup_player_information.status', $status)
            ->get();
    }

    public function accept($batch)
    {
        return $this->model->where('batch', $batch)->update([
            'status' => TopupInfo::STATUS_COMPLETE
        ]);
    }

}
