<?php

namespace Foundation\Listeners;

use Foundation\Events\EmailCampaignCreated as EmailCampaignCreatedEvent;
use Illuminate\Bus\Queueable;
use Modules\Email\Libs\Mail\Template;
use Modules\Email\Libs\Mailer;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

/**
 * Class OrderDelivered
 * @package Foundation\Listeners
 */
class EmailCampaignCreated  implements ShouldQueue
{

    use Queueable, SerializesModels;

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
        $this->mailer   = $mailer;
    }

    /**
     * Handle the event.
     *
     * @param EmailCampaignCreatedEvent $event
     * @return void
     * @throws \Throwable
     */
    public function handle(EmailCampaignCreatedEvent $event)
    {
        $campaign = $event->campaign;
        $emailList = $campaign->emailList();
        $email = $campaign->email;
        $data['email'] = $email;
        foreach ($emailList as $user){
            try {
                $mailer = $this->mailer->setSubject($email->subject)
                    ->setView('admin.email.templates.' . $email->email_markup);
                if($email->attachment)
                    $mailer->setAttachments($email->attachment);

                $mailer->send('',
                    [
                        '{FULL_NAME}'  => $user->full_name,
                        '{EMAIL}'  => $user->email_address,
                    ],
                    [
                        'sender'   => config('mail.from.address'),
                        'receiver' => $user->email_address,
                    ],
                    [
                        'custom-template' => $email->content,
                        'data' => $data
                    ]);
            } catch (\Exception $exception) {
                \Log::debug($exception->getMessage(), [
                    'subject'  => $email->subject,
                    'receiver' => $user->email_address,
                ]);
            }
        }
    }

}
