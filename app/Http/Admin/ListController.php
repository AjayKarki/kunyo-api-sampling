<?php

namespace App\Http\Controllers\Admin;

use Foundation\Models\EmailList;
use Foundation\Models\SMSList;
use Neputer\Supports\BaseController;
use Illuminate\Http\RedirectResponse;
use Foundation\Services\EmailListService;

/**
 * Class EmailListController
 * @package App\Http\Controllers\Admin
 */
class ListController extends BaseController
{

    /**
     * The EmailListService instance
     *
     * @var $emailListService
     */
    private $emailListService;

    public function __construct(EmailListService $emailListService)
    {
        $this->emailListService = $emailListService;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $emailGroup
     * @param EmailList $email
     * @return RedirectResponse
     */
    public function destroyEmail($emailGroup, EmailList $email)
    {
        $this->emailListService->delete($email);
        flash('success', 'Email is removed from Group successfully !');
        return redirect()->back();
    }

    /**
     * Remove a phone number from the list
     *
     * @param $smsGroup
     * @param SMSList $sms
     * @return RedirectResponse
     */
    public function destroySms($smsGroup, SMSList $sms)
    {
        $this->emailListService->delete($sms);
        flash('success', 'Phone is removed from Group successfully !');
        return redirect()->back();
    }
}
