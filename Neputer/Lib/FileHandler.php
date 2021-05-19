<?php

namespace Neputer\Lib;

use Throwable;
use Exception;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Contracts\Filesystem\FileNotFoundException;

/**
 * Class Scanner
 * @package Neputer\Lib
 */
final class FileHandler
{

    /**
     * @param string $key
     * @param $name
     * @param string $pattern
     * @param string $message
     * @return string|string[]
     * @throws Throwable
     */
    public function scan( string $key, $name = null, string $pattern = '*/:key:.json', string $message = ' doesnt exists.')
    {
        $path = theme_path(str_replace(':key:', $key, $pattern));

        if ($name) {
            $path = str_replace('*', $name, $path);
            throw_if(! file_exists($path), new Exception($name . $message));
        }

        return $path;
    }

    /**
     * @param string $path
     * @param array $info
     */
    public function update(string $path, array $info)
    {
        file_put_contents($path, stripslashes(json_encode($info, JSON_PRETTY_PRINT)));
    }

    /**
     * Return the given file content
     *
     * @param string $path
     * @return string
     * @throws FileNotFoundException
     */
    public function content(string $path)
    {
        return (new Filesystem())->get(realpath($path));
    }

    /**
     * @param string $path
     * @return bool
     */
    public function exists(string $path)
    {
        return (new Filesystem())->exists(realpath($path));
    }

}
