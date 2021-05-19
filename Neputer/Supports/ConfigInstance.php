<?php

namespace Neputer\Supports;

use Illuminate\Support\Arr;
use Foundation\Models\Setting;

class ConfigInstance
{

    public static function get( string $configKey )
    {
        $setting = Setting::query()->where('key', $configKey)->value('value');
        if (is_json($setting)) {
            return json_decode($setting, JSON_PRETTY_PRINT) ?? config($configKey);
        }
        return $setting ?? config($configKey);
    }

    public static function pull( string $configKey, string $key = null )
    {
        return $key ? Arr::get(static::get($configKey), $key) : static::get($configKey);
    }

}
