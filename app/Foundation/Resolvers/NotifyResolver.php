<?php

namespace Foundation\Resolvers;

use Foundation\Events\OrderPending;
use Foundation\Services\UserService;
use Foundation\Events\OrderDelivered;
use Modules\Sms\Gateway\Aakash\AakashSms;
use Modules\Sms\Gateway\Sparrow\SparrowSms;
use Modules\Sms\Gateway\Aakash\AakashConfig;
use Modules\Sms\Gateway\Sparrow\SparrowConfig;

/**
 * Class NotifyResolver
 * @package Foundation\Resolvers
 */
final class NotifyResolver
{

    /**
     * @param $transaction
     * @param string $state
     * @param bool $force
     * @param null $only [ sms/email ]
     */
    public static function notify($transaction, $state = 'pending', $force = false, $only = null)
    {
        if ($transaction) {
            if ($user = app(UserService::class)->query()
                ->select(
                    'is_sms_enabled',
                    'is_email_enabled',
                    'phone_number',
                    'email'
                )
                ->selectRaw("CONCAT(COALESCE(first_name,''),' ',COALESCE(middle_name,''),' ',COALESCE(last_name,'')) AS full_name")
                ->where('id', $transaction->user_id)
                ->first()) {

                if (!$transaction->is_notified) {

                    if ($user->is_sms_enabled && $user->phone_number) {
                        static::sendSms($transaction, $state, $user->phone_number, $user->full_name);
                    } else {
                        if ($user->is_verified) {
                            static::sendEmail($transaction, $state, $user->email, $user->full_name);
                        }
                    }

                    $transaction->update([ 'is_notified' => 1, ]);
                } elseif ($force) {

                    # $force ie case when need to send sms or email
                    if ($user->is_sms_enabled && $user->phone_number) {
                        static::sendSms($transaction, $state, $user->phone_number, $user->full_name);
                    }
                    if ($user->is_verified) {
                        static::sendEmail($transaction, $state, $user->email, $user->full_name);
                    }
                }

                if ($only) {
                    if ($only === 'sms') {
                        if ($user->is_sms_enabled && $user->phone_number) {
                            static::sendSms($transaction, $state, $user->phone_number, $user->full_name);
                        }
                    } else {
                        if ($user->is_verified) {
                            static::sendEmail($transaction, $state, $user->email, $user->full_name);
                        }
                    }
                }

            }
        }
    }

    /**
     * @param $transaction
     * @param $state
     * @param $email
     * @param $fullName
     */
    public static function sendEmail($transaction, $state, $email, $fullName)
    {
        switch ($state) {
            case "pending":
                event(new OrderPending($transaction, $email, $fullName));
                break;
            case "delivered":
                event(new OrderDelivered($transaction, $email, $fullName));
                break;
        }
    }

    /**
     * @param $transaction
     * @param $state
     * @param $phoneNumber
     * @param $fullName
     */
    public static function sendSms($transaction, $state, $phoneNumber, $fullName)
    {
        switch ($state) {
            case "pending":
                static::resolveSmsGateway()
                    ->resolveMessage('order_is_created', [
                        '{ORDER_ID}' => $transaction->transaction_id,
                        '{RECEIVER_NAME}' => $fullName,
                    ])
                    ->handle($phoneNumber);
                break;
            case "delivered":
                static::resolveSmsGateway()
                    ->resolveMessage('order_is_received', [
                        '{ORDER_ID}' => $transaction->transaction_id,
                        '{RECEIVER_NAME}' => $fullName,
                    ])
                    ->handle($phoneNumber);
                break;
            case "redeemed":
                static::resolveSmsGateway()
                    ->resolveMessage('order_is_redeemed', [
                        '{ORDER_ID}' => $transaction->transaction_id,
                        '{RECEIVER_NAME}' => $fullName,
                    ])
                    ->handle($phoneNumber);
                break;
        }
    }

    public static function sentOtpCode($otpCode, $phoneNumber)
    {
        static::resolveSmsGateway()
            ->resolveMessage('send_otp_code', [
                '{OTP_CODE}' => $otpCode,
            ])
            ->handle($phoneNumber);
    }

    public static function sentVerifiedSms($phoneNumber)
    {
        static::resolveSmsGateway()
            ->resolveMessage('phone_is_verified', [
            ])
            ->handle($phoneNumber);
    }

    /**
     * Send SMS Marketing Campaign SMS
     *
     * @param $to
     * @param $content
     * @param $placeholders
     */
    public static function sendMarketingSms($to, $content, $placeholders)
    {
        self::resolveSmsGateway()->setMessage($content, $placeholders)->handle($to);
    }

    public static function resolveSmsGateway()
    {
        if (AakashConfig::getStatus()) {
            return app(AakashSms::class);
        }

        if (SparrowConfig::getStatus()) {
            return app(SparrowSms::class);
        }
    }

    public static function sendKycNotification($number, $message)
    {
        static::resolveSmsGateway()
            ->setMessage($message, [])
            ->handle($number);
    }

}
