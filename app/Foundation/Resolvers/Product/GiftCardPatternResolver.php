<?php

namespace Foundation\Resolvers\Product;

use Foundation\Lib\GiftCard;

/**
 * Class GiftCardPatternResolver
 * @package Foundation\Resolvers\Product
 */
final class GiftCardPatternResolver
{

    /**
     * @param $pattern
     * @param $text
     * @return array|false|string[]
     */
    public static function resolve($pattern, $text)
    {
        $result = [];

        switch ($pattern) {
            case GiftCard::PATTERN_NEXT_LINE:
                $result = static::nextLineResolver($text);
                break;
            default:
                $result = static::symbolResolver($pattern, $text);
        }

        return $result;
    }

    /**
     * @param string $text
     * @return array|false|string[]
     */
    private static function nextLineResolver(string $text)
    {
        return preg_split('/\r\n|[\r\n]/', $text);
    }

    /**
     * @param $symbol
     * @param $text
     * @return false|string[]s
     */
    private static function symbolResolver($symbol, $text)
    {
        return explode($symbol, $text);
    }

}
