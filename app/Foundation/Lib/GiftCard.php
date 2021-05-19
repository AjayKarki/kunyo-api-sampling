<?php

namespace Foundation\Lib;

/**
 * Class GiftCard
 * @package Foundation\Lib
 */
final class GiftCard
{

    const PATTERN_COMMA = ',';
    const PATTERN_NEXT_LINE = '\n';
    const PATTERN_SEMI_COLON = ';';
    const PATTERN_HYPHEN = '-';
    const PATTERN_UNDERSCORE = '_';

    public static function patterns()
    {
        return [
            self::PATTERN_NEXT_LINE  => 'Next Line',
            self::PATTERN_COMMA      => 'Comma ( , )',
            self::PATTERN_SEMI_COLON => 'Semi colon ( ; )',
            self::PATTERN_HYPHEN     => 'Hyphen ( - )',
            self::PATTERN_UNDERSCORE => 'Underscore ( _ )',
        ];
    }

}
