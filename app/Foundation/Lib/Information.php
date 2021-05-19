<?php

namespace Foundation\Lib;

use Foundation\Lib\User\UserInfo;

final class Information
{

    use UserInfo;

    public static function getCurrentUrl(): string
    {
        return url()->previous();
    }

    public static function getIp(): ?string
    {
        return  request()->ip();
    }

}
