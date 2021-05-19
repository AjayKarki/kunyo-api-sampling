<?php


namespace Foundation\Services;


use Foundation\Models\TransactionConversation;
use Neputer\Supports\BaseService;

class TransactionConversationService extends BaseService
{
    /**
     * @var TransactionConversation
     */
    public $model;

    public function __construct(TransactionConversation $conversation)
    {
        $this->model = $conversation;
    }

    /**
     * Mark Admin Messages as Read
     *
     * @param $transactionId
     */
    public function markAsRead($transactionId)
    {
        $this->model->where('transaction_id', $transactionId)->where('acknowledged', false)
            ->update([
                'acknowledged' => true
            ]);
        return;
    }

    /**
     * Mark Messages read by Admin
     *
     * @param $transactionId
     */
    public function markAsReadByAdmin($transactionId)
    {
        $this->model->where('transaction_id', $transactionId)->where('acknowledged_by_admin', false)
            ->update([
                'acknowledged_by_admin' => true
            ]);
        return;
    }

}
