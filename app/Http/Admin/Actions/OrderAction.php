<?php


namespace App\Http\Controllers\Admin\Actions;

use Illuminate\Http\Request;
use Foundation\Services\TransactionConversationService as ConversationService;

/**
 * Class OrderAction
 * @package App\Http\Controllers\Admin\Actions
 */
class OrderAction
{
    /**
     * @var ConversationService
     */
    private $conversationService;

    /**
     * OrderAction constructor.
     * @param ConversationService $conversationService
     */
    public function __construct(ConversationService $conversationService)
    {
        $this->conversationService = $conversationService;
    }

    /**
     * Save comment of Transaction
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addComment(Request $request)
    {
        $request->validate([
            'message' => 'required',
            'transaction_id' => 'required'
        ]);

        $comment = $this->conversationService->new($request->all() + ['author_id' => auth()->user()->id]);
        $comment->load('author:id,first_name,middle_name,last_name');
        $comment->author['url'] = route('admin.user.show', $comment->author->id);
        $comment->author['name'] = $comment->author->getFullName();
        return response()->json(['msg' => 'Comment Added', 'comment' => $comment]);
    }
}
