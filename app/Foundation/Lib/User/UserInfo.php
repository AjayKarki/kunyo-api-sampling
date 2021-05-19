<?php

namespace Foundation\Lib\User;

use Foundation\Models\User;

trait UserInfo
{

    public static function getFullName($loggedIn = false, User $user = null): string
    {
        if ($loggedIn) {
            $user = auth()->user();
        }
        return optional($user)->getFullName();
    }

    public static function getEmail($loggedIn = false, User $user = null): string
    {
        if ($loggedIn) {
            $user = auth()->user();
        }
        return optional($user)->email;
    }

    public static function getId($loggedIn = false, User $user = null): string
    {
        if ($loggedIn) {
            $user = auth()->user();
        }
        return optional($user)->id;
    }

    public static function getUser($username)
    {
        return User::query()
            ->where(function ($query) use ($username) {
                $query->orWhere('email', $username)
                    ->orWhere('phone_number', $username);
            })
            ->first();
    }

    public static function hasHighAccess($user)
    {
        return optional($user)->hasHighAccess();
    }

}
