<?php

namespace Neputer\Supports\Theme\Engines;

use Neputer\Lib\FileHandler;

/**
 * Class ThemeEditor
 * @package Neputer\Supports\Theme\Engines
 */
final class ThemeEditor
{

    /**
     * Update the theme
     *
     * @param string $themePath
     * @param array $themeInfo
     */
    public static function update(string $themePath, array $themeInfo)
    {
        (new FileHandler())->update($themePath, $themeInfo);
    }

}
