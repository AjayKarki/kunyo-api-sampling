<?php

namespace Neputer\Lib;

/**
 * Class Randomify
 *
 * Generate random string with a length of given $length parameter
 *
 * @package Neputer\Lib
 * @since 0.1.0
 * @version 0.1.0
 */
class Randomify
{

    /**
     * A string of all possible character's
     *
     * @var string
     */
    private $chars = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";

    /**
     * @param int $length
     * @return string
     * @throws \Exception
     */
    public function generate(int $length = 12)
    {
        $pieces = [];
        $max    = mb_strlen( $this->chars, '8bit' ) - 1;
        for ( $i = 0; $i < $length; ++$i ) {
            $pieces[] = $this->chars[ random_int( 0, $max ) ];
        }
        return implode('', $pieces);
    }

}
