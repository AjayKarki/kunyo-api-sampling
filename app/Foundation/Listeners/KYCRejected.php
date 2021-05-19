<?php

namespace Foundation\Listeners;

use Foundation\Events\KYCRespond;
use Foundation\Lib\KYC;

use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Email\Libs\Mailer;

class KYCRejected implements ShouldQueue
{
    /**
     * @var Mailer
     */
    private $mailer;

    /**
     * Create the event listener.
     *
     * @param Mailer $mailer
     */
    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * Handle the event.
     *
     * @param $event
     * @return void
     */
    public function handle(KYCRespond $event)
    {
        $kyc = $event->kyc;
        if($kyc->verification_status != KYC::STATUS_REJECTED)
            return;

        $fields = [
            '{KYC_APPLICANT_NAME}' => $kyc->first_name . ' ' . $kyc->last_name,
            '{KYC_REMARKS}' =>  $kyc->remarks,
        ];


        try {
            $this->mailer
                ->setSubject('Kunyo.co | KYC Verification Request Rejected')
                ->setView('email.template')
                ->send('kyc_rejected_template', $fields, [
                    'receiver' => $kyc->email,
                    'sender'   => config('mail.from.address'),
                ]);
        } catch (\Exception $exception) {
            \Log::debug($exception->getMessage(), [
                'subject'  => 'Kunyo.co | KYC Verification Request Rejected',
                'receiver' => $kyc->email,
                'sender'   => config('mail.from.address'),
            ]);
        }
    }
}
