<?php

namespace Neputer\Supports\Access;

/**
 * Trait HasAccess
 * @package Neputer\Supports\Access
 */
trait HasAccess
{

    /**
     * Override of callAction to perform the authorization before
     *
     * @param $method
     * @param $parameters
     * @return mixed
     */
    public function callAction($method, $parameters)
    {
        if ($access = $this->getAccess()) {
            $this->authorize($access);
        }
        return parent::callAction($method, $parameters);
    }

    /**
     * Return access name aka permission
     *
     * @return string|string[]
     */
    private function getAccess()
    {
        return str_replace(
            config('permission.black-listed-chars', [ '.', ]),
            config('permission.glue', '_'),
            request()->route()->getName()
        );
    }

}
