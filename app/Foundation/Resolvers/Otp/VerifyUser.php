<?php

namespace Foundation\Resolvers\Otp;

use Foundation\Lib\Verify;
use Illuminate\Support\Str;
use Foundation\Models\User;
use Foundation\Resolvers\NotifyResolver;
use Illuminate\Contracts\Auth\Authenticatable;

/**
 * Class VerifyUser
 * @package Foundation\Resolvers\Otp
 */
final class VerifyUser
{

    /**
     * @param User $user
     * @param $email
     */
    public static function sendEmail(User $user, $email)
    {
        if ($user) {
            $tries = 0;

            $userId = $user->id;

            $user->update([
                'email' => $email,
            ]);

            // Get the old verification to increment the flag tries
            $verification = app('db')
                ->table('user_verifications')
                ->where('user_id', $userId)
                ->orderBy('created_at', 'DESC')
                ->first();

            if ($verification) {
                $tries = $verification->tries;
            }

            app('db')
                ->table('user_verifications')
                ->updateOrInsert([ 'user_id' => $userId, ], [
                    'token'      => md5(Str::random(16) . microtime()),
                    'tries'      => $tries + 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

            event(new \Foundation\Events\VerifyUser($user));
        }
    }

    /**
     * Send the otp code to given number
     *
     * @param Authenticatable $user
     * @param $phoneNumber
     */
    public static function sendOtp(Authenticatable $user, $phoneNumber)
    {
        if ($user) {
            $tries = 0;

            $userId = $user->id;

            $user->update([
                'phone_number' => $phoneNumber,
            ]);

            // Get the old verification to increment the flag tries
            $verification = app('db')
                ->table('user_sms_verifications')
                ->where('user_id', $userId)
                ->orderBy('created_at', 'DESC')
                ->first();

            if ($verification) {
                $tries = $verification->tries;
            }

            if (app('db')
                ->table('user_sms_verifications')
                ->updateOrInsert([ 'user_id' => $userId, ], [
                    'otp_code'   => static::resolveOtp(),
                    'tries'      => $tries + 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ])) {

                $otpCode = app('db')
                    ->table('user_sms_verifications')
                    ->where('user_id', $userId)
                    ->orderBy('created_at', 'DESC')
                    ->value('otp_code');

                NotifyResolver::sentOtpCode(
                    $otpCode,
                    $phoneNumber
                );

            }
        }
    }

    /**
     * Resolve the otp code
     *
     * @param null $code
     * @return int|mixed
     */
    public static function resolveOtp($code = null)
    {
        $otp = $code ? $code : mt_rand(100000,999999);

        if (app('db')
            ->table('user_sms_verifications')
            ->where('otp_code', $otp)->first()) {
            return static::resolveOtp(mt_rand(100000,999999));
        }

        return $otp;
    }

    /**
     * Verify Phone number
     *
     * @param $userId
     * @param $otpCode
     * @param $phoneNumber
     * @return bool
     */
    public static function verifyPhoneNumber($userId, $otpCode, $phoneNumber)
    {
        if (app('db')->table('user_sms_verifications')
                ->where( 'user_id', $userId)
                ->orderBy('created_at', 'DESC')
                ->value('otp_code') == $otpCode) {

            return app('db')
                ->table('users')
                ->where( 'id', $userId)
                ->where('phone_number', $phoneNumber)
                ->update([
                    'phone_verified_at' => now(),
                    'phone_is_verified' => 1,
                ]);
        }
        return false;
    }

    /**
     * Verify email
     *
     * @param $userId
     * @param $verificationCode
     * @param $email
     * @return bool
     */
    public static function verifyEmailAddress($userId, $verificationCode, $email)
    {
        if (app('db')
            ->table('user_verifications')
            ->where('token', $verificationCode)
            ->where('user_id', $userId)
            ->orderBy('created_at', 'DESC')
            ->first()) {

            return app('db')
                ->table('users')
                ->where('id', $userId)
                ->where('email', $email)
                ->update([
                'is_verified'       => 1,
                'email_verified_at' => now(),
            ]);
        }
        return false;
    }

    /**
     * Resend won't be available if it's greater or equals to max tries
     *
     * @param string $notifier
     * @param $userId
     * @return bool
     */
    public static function isResendAvailable(string $notifier, $userId)
    {
        $canResend = false;

        $maxTries = Verify::MAX_TRIES;

        if ($notifier === Verify::NOTIFIER_EMAIL) {
            $canResend = static::getEmailTried($userId) < $maxTries;
        } else if ($notifier === Verify::NOTIFIER_PHONE) {
            $canResend = static::getSmsTried($userId) < $maxTries;
        }

//        if (optional($verification)->tries >= Verify::MAX_TRIES) {
//            // Cool down time
//            $canResend = $verification->updated_at->diffInHours() >= Verify::MAX_TRIES_COOL_DOWN;
//        }

        return $canResend;
    }

    /**
     * Return tries quantity for given email
     *
     * @param $userId
     * @return mixed|null
     */
    public static function getEmailTried($userId)
    {
        return static::tries('user_verifications', $userId);
    }

    /**
     * Return tries quantity for given sms
     *
     * @param $userId
     * @return mixed|null
     */
    public static function getSmsTried($userId)
    {
        return static::tries('user_sms_verifications', $userId);
    }

    /**
     * Return tries quantity for given sms/email
     *
     * @param $model
     * @param $userId
     * @return mixed|null
     */
    private static function tries($model, $userId)
    {
        return app('db')
            ->table($model)
            ->where( 'user_id', $userId)
            ->value('tries') ?? 0;
    }

    /**
     * @param $userId
     */
    public static function deactivateUser($userId)
    {
        app('db')
            ->table('user_verifications')
            ->where('user_id', $userId)
            ->update([ 'is_email_valid' => 0, ]);

        app('db')
            ->table('users')
            ->where('id', $userId)
            ->update([ 'is_deactivated' => 1, ]);
    }

    public static function isEmailValid($userId)
    {
        return app('db')
            ->table('user_verifications')
            ->where('user_id', $userId)
            ->value('is_email_valid');
    }

}
