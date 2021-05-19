<?php

namespace Foundation\Resolvers\Message;

use Foundation\Lib\Verify;
use Neputer\Supports\Utility;
use Foundation\Config\Notifier\NotifierConfig;

/**
 * Class NotifierMessage
 * @package Foundation\Resolvers\Message
 */
final class NotifierMessage
{

    /**
     * @param string $key
     * @param array $placeholders
     * @return string|string[]|null
     */
    public static function extract(string $key, $placeholders = [])
    {
        return static::resolvePatterns( NotifierConfig::extract($key), $placeholders );
    }

    /**
     * @param $content
     * @param array $placeholders
     * @return string|string[]|null
     */
    private static function resolvePatterns($content, array $placeholders = [])
    {
        return Utility::resolvePatterns( $content ?? '', array_merge( [
            '{AUTH_FULL_NAME}' => static::getAuthFullName(),
            '{MAX_TRIES}'      => Verify::MAX_TRIES,
        ], $placeholders));
    }

    /**
     * @return mixed
     */
    private static function getAuthFullName()
    {
        return ucwords(optional(auth()->user())->getFullName());
    }

}
