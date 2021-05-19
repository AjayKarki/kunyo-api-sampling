<?php

namespace Foundation\Config;

use Neputer\Supports\ConfigInstance;

/**
 * Class SettingConfig
 * @package Foundation\Config
 */
final class SettingConfig extends ConfigInstance
{

    public static function extract($configKey, $key = null)
    {
        return static::pull($configKey, $key);
    }

}
