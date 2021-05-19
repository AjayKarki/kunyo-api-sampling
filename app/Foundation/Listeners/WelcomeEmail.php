<?php

namespace Foundation\Listeners;

use Illuminate\Bus\Queueable;
use Modules\Email\Libs\Mailer;
use Foundation\Events\UserCreated;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Foundation\Resolvers\Exception\EmailAddress;

/**
 * Class WelcomeEmail
 * @package Foundation\Listeners
 */
class WelcomeEmail implements ShouldQueue
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
    public function __construct(
        Mailer $mailer
    )
    {
        $this->mailer   = $mailer;
    }

    /**
     * Handle the event.
     *
     * @param UserCreated $event
     * @return void
     */
    public function handle(UserCreated $event)
    {
        try {
            $this->mailer
                ->setSubject('Welcome to Kunyo.co')
                ->setView('email.template')
                ->send('email_welcome_template', [
                    '{APP_NAME}'  => config('app.name'),
                    '{WEB_LINK}'  => url('/'),
                    '{SITE_URL}'  => url('/'),
                    '{USER_NAME}' => join(' ', [
                        $event->user->first_name, $event->user->middle_name, $event->user->last_name,
                    ]),
                ], [
                    'receiver' => $event->user->email,
                    'sender'   => config('mail.from.address'),
                ]);
        } catch (\Exception $exception) {

            if (EmailAddress::isInvalid($exception)) {
                \Foundation\Resolvers\Otp\VerifyUser::deactivateUser(optional($event->user)->id);
            }

            \Log::debug($exception->getMessage(), [
                'subject'  => 'Kunyo - Welcome to Kunyo',
                'receiver' => $event->user->email,
            ]);
        }
    }

}
