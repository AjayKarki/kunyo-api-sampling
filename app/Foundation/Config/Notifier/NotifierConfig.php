<?php

namespace Foundation\Config\Notifier;

use ArrayAccess;
use Foundation\Lib\Meta;

/**
 * Class NotifierConfig
 * @package Foundation\Config\Notifier
 */
final class NotifierConfig
{

    const CONFIG_KEY = 'notifier';

    /**
     * @param null $key
     * @return array|ArrayAccess|mixed
     */
    public static function extract($key = null)
    {
        $key = isset($key) ? '.'.$key : '';
        return Meta::args(static::CONFIG_KEY.$key);
    }

}
