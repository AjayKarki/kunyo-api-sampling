<?php

namespace Foundation\Listeners;

use Foundation\Models\User;
use Illuminate\Bus\Queueable;
use Modules\Email\Libs\Mailer;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Foundation\Resolvers\EmailOrderResolver;
use Foundation\Events\OrderPending as Pending;
use Foundation\Resolvers\Exception\EmailAddress;

/**
 * Class VerifyUser
 * @package Foundation\Listeners
 */
class VerifyUser implements ShouldQueue
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
     * @param User $event
     * @return void
     * @throws \Throwable
     */
    public function handle($event)
    {
        $token = app('db')
            ->table('user_verifications')
            ->where('user_id', $event->user->id)
            ->orderBy('created_at', 'DESC')
            ->value('token');
        try {
            $this->mailer
                ->setSubject('Kunyo - User Verification')
                ->setView('email.email-registration-template')
                ->send('email_verification_template', [
                    '{APP_NAME}'  => config('app.name'),
                    '{WEB_LINK}'  => url('/'),
                    '{SITE_URL}'  => url('/'),
                    '{USER_NAME}' => join(
                        ' ', [ $event->user->first_name, $event->user->middle_name, $event->user->last_name, ]
                    ),
                    '{TOKEN_VERIFICATION_URL}' => route('verification.verify',  $token),
                    '{VERIFICATION_TOKEN}' => $token,
                ], [
                    'receiver' => $event->user->email,
                    'sender'   => config('mail.from.address'),
                ]);
        } catch (\Exception $exception) {

            if (EmailAddress::isInvalid($exception)) {
                \Foundation\Resolvers\Otp\VerifyUser::deactivateUser(optional($event->user)->id);
            }

            \Log::debug($exception->getMessage(), [
                'subject'  => 'Kunyo - User Verification',
                'receiver' => $event->user->email,
                'errorTrace' => $exception->getTraceAsString(),
            ]);
        }
    }

}
