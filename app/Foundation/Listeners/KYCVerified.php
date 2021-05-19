<?php

namespace Foundation\Listeners;

use Foundation\Events\KYCRespond;
use Foundation\Lib\KYC;

use Foundation\Resolvers\NotifyResolver;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Email\Libs\Mailer;

class KYCVerified implements ShouldQueue
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
        $kyc = $event->kyc->load('user:id,phone_number');
        if($kyc->verification_status != KYC::STATUS_VERIFIED)
            return;

        $fields = [
            '{KYC_APPLICANT_NAME}' => $kyc->first_name . ' ' . $kyc->last_name,
            '{KYC_REMARKS}' =>  $kyc->remarks,
        ];


        try {
            $this->mailer
                ->setSubject('Kunyo.co | KYC Verified')
                ->setView('email.template')
                ->send('kyc_verified_template', $fields, [
                    'receiver' => $kyc->email,
                    'sender'   => config('mail.from.address'),
                ]);
        } catch (\Exception $exception) {
            \Log::debug($exception->getMessage(), [
                'subject'  => 'Kunyo.co | KYC Verified',
                'receiver' => $kyc->email,
                'sender'   => config('mail.from.address'),
            ]);
        }

        $message = 'Hello ' . $kyc->first_name . '. The KYC information you submitted at Kunyo is verified. Kunyo.co.';
        NotifyResolver::sendKycNotification($kyc->user->phone_number, $message);
    }
}
