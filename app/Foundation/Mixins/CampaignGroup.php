<?php


namespace Foundation\Mixins;


use Foundation\Services\UserService;
use Illuminate\Http\Request;

trait CampaignGroup
{
    /**
     * @var UserService
     */
    private $userService;

    /**
     * CampaignGroup constructor.
     * @param UserService $userService
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Get Email or SMS List
     *
     * @param Request $request
     * @param $parent
     * @param $type
     * @return array
     */
    public function getList(Request $request, $parent, $type)
    {
        if($request->get('added_from') == 'manual')
            $list = $this->getManualUserDetails($request->get('manual_users'), $parent->id, $request->get('added_from'), $type);
        else
            $list = $this->getUserDetails($request->get('users'), $parent->id, $request->get('added_from'), $type);
        return $list;
    }

    /**
     * Prepare a list of Emails to insert into Email Lists table
     *
     * @param $userIds
     * @param $groupId
     * @param $addedFrom
     * @param $type
     * @return array
     */
    private function getUserDetails($userIds, $groupId, $addedFrom, $type)
    {
        $users = $this->userService->find($userIds);

        $emailList = [];
        foreach($users as $user) {
            $item = [];
            $item['group_id'] = $groupId;
            $item['user_id'] = $user->id;
            $item['full_name'] = $user->first_name . ' ' . $user->middle_name . ' ' . $user->last_name;
            $item['email_address'] = $user->email;
            $item['phone_number'] = $user->phone_number;
            $item['added_from'] = $addedFrom;
            $item['type'] = $type;
            $item['created_at'] = now();
            $item['updated_at'] = now();
            array_push($emailList, $item);
        }
        return $emailList;
    }

    /**
     * Prepare Email List for manual users entry
     *
     * @param $users
     * @param $groupId
     * @param $addedFrom
     * @param $type
     * @return array
     */
    private function getManualUserDetails($users, $groupId, $addedFrom, $type)
    {
        $emailList = [];
        foreach($users as $user) {
            $item = [];
            $item['group_id'] = $groupId;
            $item['user_id'] = null;
            $item['full_name'] = $user['full_name'];
            $item['email_address'] = $user['email_address'] ?? null;
            $item['phone_number'] = $user['phone_number'] ?? null;
            $item['added_from'] = $addedFrom;
            $item['type'] = $type;
            $item['created_at'] = now();
            $item['updated_at'] = now();
            array_push($emailList, $item);
        }
        return $emailList;
    }
}
