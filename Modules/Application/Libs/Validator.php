<?php

namespace Modules\Application\Libs;

/**
 * Class Validator
 * @package Modules\Application\Libs
 */
final class Validator
{

    public static function resolve($errors)
    {
        $resolved = [];

        foreach ($errors as $error => $messages) {
            $resolved[$error] = current($messages) ?? 'N/A';
        }

        return $resolved;
    }

}
