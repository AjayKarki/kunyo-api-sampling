<?php

namespace Foundation\Handler;

use Neputer\Config\Status;
use Foundation\Lib\Spammer;

/**
 * Class SpammerAlert
 * @package Foundation\Handler
 */
final class SpammerAlert
{

    const DEV_TEAM_EMAILS = [
        'sachinrai@neputer.com',
        'madan2056@gmail.com',
    ];

    public static function notify($transaction)
    {
        SpammerAlert::blacklistUser(auth()->id());

        SpammerAlert::log($transaction);

        SpammerAlert::notifyToDev($transaction);
    }

    private static function blacklistUser($id)
    {
        app('db')
            ->table('users')
            ->where('id', $id)
            ->update([
                'updated_at'        => now(),
                'is_blacklisted'    => Status::ACTIVE_STATUS,
                'blacklist_reason'  => Spammer::BLACKLIST_REASON_SPAMMER,
            ]);

        auth()->logout();
    }

    private static function log($transaction)
    {
        \Log::emergency(optional(auth()->user())->getFullName() .' is trying to spam. User ID : '. auth()->id() . ' , TransactionId : '. optional($transaction)->id, [
            'user'   => auth()->user(),
            'transaction' => $transaction->toArray(),
        ]);
    }

    private static function notifyToDev ($transaction)
    {
        try {
            \Mail::send('email.email-spammer-alert', [
                'transaction' => $transaction,
                'user'        => auth()->user(),
            ], function($message) {
                $message->to(
                    SpammerAlert::DEV_TEAM_EMAILS
                )->subject('Neputer Tech Pvt LTD | Spammer Alert !');
            });
        } catch (\Exception $exception) {
            \Log::emergency('Spammer Alert Email Issue fixes!');
        }
    }

}
