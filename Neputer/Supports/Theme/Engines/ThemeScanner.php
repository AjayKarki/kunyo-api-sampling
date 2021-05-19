<?php

namespace Neputer\Supports\Theme\Engines;

use Throwable;
use Exception;
use Neputer\Lib\FileHandler;
use Illuminate\Support\Collection;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Contracts\Filesystem\FileNotFoundException;

/**
 * Class ThemeScanner
 * @package Neputer\Supports\Theme\Engines
 */
final class ThemeScanner
{

    const THEME_KEY  = 'theme';

    /**
     * @var $themePath
     */
    private $themePath = null;

    /**
     * Return the scanned files
     *
     * @return Collection
     * @throws Throwable
     */
    public function get()
    {
        return Collection::make($this->getThemes())
            ->map(function ($theme) {
                return $this->retrieveThemeInfo($theme);
            });
    }

    /**
     * Return bool
     *
     * @param string $theme
     * @return bool
     * @throws Throwable
     */
    public function isThemeAvailable(string $theme)
    {
        return in_array($theme, $this->getAvailableThemes());
    }

    /**
     * Return available themes
     *
     * @param string $key
     * @return array
     * @throws Throwable
     */
    public function getAvailableThemes(string $key = self::THEME_KEY)
    {
        if (! $this->themePath) {
            $this->scan($key);
        }
        return $this->get()
            ->pluck('alias')
            ->toArray();
    }

    /**
     * @return array|false
     * @throws Throwable
     */
    public function getThemes()
    {
        return glob($this->getThemePath());
    }

    /**
     * Retrieve theme info
     *
     * @param string $themePath
     * @param string|null $key
     * @return mixed
     * @throws FileNotFoundException
     */
    public function retrieveThemeInfo(string $themePath, string $key = null)
    {
        $themeInfo = json_decode((new Filesystem)->get($themePath), true);

        if ($key) {
            return $themeInfo[$key] ?? null;
        }

        return $themeInfo;
    }

    /**
     * Return the theme path
     *
     * @param string $key
     * @param string $themeName
     * @return null|string
     * @throws Throwable
     */
    public function getThemePath(string $key = self::THEME_KEY, string $themeName = null)
    {
        if (! $this->themePath) {
            $this->scan($key, $themeName);
        }
        return $this->themePath;
    }

    /**
     * Scan the json
     *
     * @param string $key
     * @param string|null $themeName
     * @return $this
     * @throws Throwable
     */
    public function scan( string $key = self::THEME_KEY, string $themeName = null )
    {
        $this->themePath = (new FileHandler())
            ->scan($key, $themeName, '*/:key:.json', ' theme doesnt exists.');
        return $this;
    }

}
