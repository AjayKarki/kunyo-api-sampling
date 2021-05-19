<?php

namespace Foundation\Lib;

use Foundation\Models\User;
use App\Foundation\Lib\History;
use Foundation\Services\HistoryService;

final class KunyoTracker
{

    /**
     * Track only the higher privileges login failed
     *
     * @param $userName
     */
    public static function trackLoginFailed($userName)
    {
        $user = Information::getUser($userName);

        if ($user && Information::hasHighAccess($user->email)) {
            KunyoTracker::setHistory([
                'title'            => 'High Access User login : Failed' . $user->email,
                'information'      => '( '.$user->id.' )'. $user->fullName() .' is trying to login from : ' . Information::getCurrentUrl() . ' & IP : '. Information::getIp(),
                'user_id'          => $user->id,
                'user_name'        => $user->fullName(),
                'type'             => History::TYPE_LOGIN,
                'historyable_id'   => $user->id,
                'historyable_type' => User::class
            ]);
        }
    }

    /**
     * Track only the higher privileges login success
     *
     * @param $user
     */
    public static function trackHighAccess($user)
    {
        if (request()->user()->hasHighAccess()) {
            KunyoTracker::loginStats($user);
        }
    }

    private static function loginStats($user)
    {
        $now = now();

        $fullName = Information::getFullName(false, $user);
        $email    = Information::getEmail(false, $user);
        $userId   = Information::getId(false, $user);

        $url      = Information::getCurrentUrl();
        $ip       = Information::getIp();

        \Log::notice("Login Stats : " . $email, [
            'User Name'      => $fullName,
            'User Email'     => $email,
            'User Id'        => $userId,
            'Last Login at'  => $now,
            'Ip Address'     => $ip,
            'endpoint'       => $url,
        ]);

        KunyoTracker::setHistory([
            'title'            => 'High Access User login : Success',
            'information'      => '( '.$userId.' )'. $fullName .' is logged in from : ' . Information::getCurrentUrl() . ' & IP : '. Information::getIp(),
            'user_id'          => $userId,
            'user_name'        => $fullName,
            'type'             => History::TYPE_LOGIN,
            'historyable_id'   => $userId,
            'historyable_type' => User::class
        ]);

        request()->user()->update([
            'last_login' => $now,
        ]);
    }

    private static function setHistory(array $data)
    {
        app(HistoryService::class)
            ->new($data);
    }

}
