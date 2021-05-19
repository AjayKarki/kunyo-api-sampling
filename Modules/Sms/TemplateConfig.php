<?php

namespace Modules\Sms;

use Neputer\Supports\ConfigInstance;

/**
 * Class TemplateConfig
 * @package Modules\Sms
 */
final class TemplateConfig extends ConfigInstance
{

    const CONFIG_KEY = 'sms';

    public static function getTemplateContent(string $template)
    {
        return static::pull(self::CONFIG_KEY, 'template.'.$template);
    }

}
