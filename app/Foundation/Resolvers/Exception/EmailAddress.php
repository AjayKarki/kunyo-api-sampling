<?php

namespace Foundation\Resolvers\Exception;

use Exception;
use Illuminate\Support\Str;

/**
 * Class EmailAddress
 * @package Foundation\Resolvers\Exception
 */
final class EmailAddress
{

    /**
     * @param Exception $exception
     * @return bool
     */
    public static function isInvalid(Exception $exception)
    {
        return Str::contains($exception->getMessage(), '559 Invalid rcptto');
    }

}
