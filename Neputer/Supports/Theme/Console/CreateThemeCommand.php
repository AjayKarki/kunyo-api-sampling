<?php

namespace Neputer\Supports\Theme\Console;

use Throwable;
use Illuminate\Console\Command;
use Exception as ThemeFoundException;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Config\Repository as Config;
use Neputer\Supports\Theme\Engines\ThemeScanner;
use Illuminate\Contracts\Filesystem\FileNotFoundException;

/**
 * Class CreateThemeCommand
 * @package Neputer\Supports\Theme\Console
 */
class CreateThemeCommand extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'theme:create {name} {--no-mix : No webpack mix} {--force : Force will recreate the theme}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate the theme default structure';

    /**
     * @var ThemeScanner
     */
    private $scanner;

    /**
     * @var Filesystem
     */
    private $file;

    /**
     * @var Config
     */
    private $config;

    /**
     * DestroyThemeCommand constructor.
     *
     * @param Config $config
     * @param ThemeScanner $themeScanner
     * @param Filesystem $file
     */
    public function __construct( Config $config, ThemeScanner $themeScanner, Filesystem $file )
    {
        parent::__construct();
        $this->scanner = $themeScanner;
        $this->file = $file;
        $this->config = $config;
    }

    /**
     * Execute the console command.
     *
     * @return void
     * @throws Throwable
     */
    public function handle()
    {
        $selectedTheme = strtolower($this->argument('name'));

        if ($this->option('force') && $this->confirm(
                'Do you really wish to force ? (re-creates your entire theme if already available)')
        ) {
            $this->call('theme:destroy', [ 'name' => $selectedTheme, ]);
        }

        try {

            throw_if(
                $this->scanner->isThemeAvailable($selectedTheme),
                new ThemeFoundException('Theme `' . $selectedTheme . '` is already created.')
            );

        } catch (\Exception $exception) {
            $this->info($exception->getMessage());
            exit();
        }

        $this->generateDefaultStructures($selectedTheme);
        $this->generateConfigs($selectedTheme);
        $this->generateAssets($selectedTheme);
        $this->generateMix($selectedTheme);
        $this->generateLang($selectedTheme);
        $this->generateRoute($selectedTheme);

        $this->info('Theme `' . $selectedTheme . '` is successfully created.');
    }

    /**
     * @param string $theme
     */
    private function generateDefaultStructures(string $theme)
    {
        $directories = array_values($this->config->get('theme.directories', [
            'assets'  => 'assets',
            'lang'    => 'lang',
            'view'    => 'views',
        ]));

        $custom = $this->config->get('theme.custom', [
            'views/shared',
            'views/layouts',
        ]);

        $assets =  array_values($this->getAssetConfig());

        $directories = array_merge($directories, $custom, $assets);

        foreach ($directories as $dir) {

            if (in_array($dir, $assets)) {
                $assetDirectoryName = strtolower($this->config->get('theme.directories.assets', 'assets'));
                $dir = $assetDirectoryName . DIRECTORY_SEPARATOR . $dir;
            }
            $this->mkdir($this->baseDir($theme) . $dir);
        }
    }

    /**
     * @param string $theme
     * @throws FileNotFoundException
     */
    private function generateConfigs(string $theme)
    {
        $this->makeFile($this->baseDir($theme). 'theme.json',
            $this->resolveStub('theme', $this->resolveArgs($theme)));
    }

    /**
     * @param string $theme
     * @throws FileNotFoundException
     */
    private function generateMix(string $theme)
    {
        $stub = $this->option('no-mix') ? 'no-mix' : 'webpack.mix';
        $this->makeFile($this->baseDir($theme). 'webpack.mix.js',
            $this->resolveStub($stub, $this->resolveArgs($theme)));
        $this->file->append(base_path('webpack.mix.js'), $this->resolveStub('require.mix', $this->resolveArgs($theme)));
    }

    /**
     * @param string $theme
     * @throws FileNotFoundException
     */
    private function generateLang(string $theme)
    {
        $languages = $this->config->get('theme.lang', [ 'en', ]);

        foreach ($languages as $language) {
            $path = $this->baseDir($theme) . $this->config->get('theme.directories.lang') . DIRECTORY_SEPARATOR . strtolower($language) . DIRECTORY_SEPARATOR;
            $this->mkdir($path);
            $this->makeFile($path. 'index.php',
                $this->resolveStub('index.lang', $this->resolveArgs($theme)));
        }
    }

    /**
     * @param string $theme
     * @throws FileNotFoundException
     */
    private function generateAssets(string $theme)
    {
        $assets = $this->config->get('theme.assets', [
            'css'     => 'css',
            'img'     => 'img',
            'js'      => 'js',
        ]);

        $assetDirectoryName = strtolower($this->config->get('theme.directories.assets', 'assets'));

        foreach ($assets as $key => $dir) {
            if ($key !== 'img') {
                $this->makeFile($this->baseDir($theme). $assetDirectoryName . DIRECTORY_SEPARATOR . strtolower($dir) . DIRECTORY_SEPARATOR . 'app.'.$key,
                    $this->resolveStub('assets'.DIRECTORY_SEPARATOR.'app', $this->resolveArgs($theme)));
            }
        }
    }

    /**
     * @param string $theme
     * @throws FileNotFoundException
     */
    private function generateRoute(string $theme)
    {
        $this->makeFile($this->baseDir($theme) . 'route.php',
            $this->resolveStub('route', $this->resolveArgs($theme)));
    }

    /**
     * @param string $theme
     * @return string
     */
    private function baseDir(string $theme)
    {
        return theme_path(strtolower($theme)) . DIRECTORY_SEPARATOR;
    }

    /**
     * @param $file
     * @param null $template
     */
    private function makeFile($file, $template = null)
    {
        if ( ! $this->file->exists($file))
        {
            $this->file->put($file, $template);
        }
    }

    /**
     * Make Directory
     *
     * @param $path
     */
    private function mkdir($path)
    {
        if ( ! $this->file->isDirectory($path))
        {
            $this->file->makeDirectory($path, 0777, true);
        }
    }

    /**
     * @param $template
     * @param array $replacements
     * @return mixed|string
     * @throws FileNotFoundException
     */
    private function resolveStub($template, $replacements = [])
    {
        $path = realpath(__DIR__ . DIRECTORY_SEPARATOR .'stubs'.DIRECTORY_SEPARATOR . $template . '.stub');
        $content = $this->file->get($path);
        if (!empty($replacements)) {
            $content = str_replace(array_keys($replacements), array_values($replacements), $content);
        }
        return $content;
    }

    private function resolveArgs($theme)
    {
        return [
            '{CAP_THEME}' => ucfirst($theme),
            '{THEME}'     => strtolower($theme),
            '{SITE_NAME}' => config('app.name'),

            '{IMAGE_PATH}' => $this->getThemeAssetPath($theme, 'img'),
            '{IMAGE_PUBLIC_PATH}' => $this->getPublicAssetPath($theme, 'img'),
            '{JS_PATH}' => $this->getThemeAssetPath($theme, 'js') . DIRECTORY_SEPARATOR . 'app.js',
            '{JS_PUBLIC_PATH}' => $this->getPublicAssetPath($theme, 'js') . DIRECTORY_SEPARATOR . 'app.min.js',
            '{CSS_PATH}' => $this->getThemeAssetPath($theme, 'css') . DIRECTORY_SEPARATOR . 'app.css',
            '{CSS_PUBLIC_PATH}' => $this->getPublicAssetPath($theme, 'css') . DIRECTORY_SEPARATOR . 'app.min.css',

            '{ASSET_PATH}' => 'resources/themes'. DIRECTORY_SEPARATOR . strtolower($theme) . DIRECTORY_SEPARATOR . $this->config->get('theme.directories.assets', 'assets'),
            '{ASSET_PUBLIC_PATH}' => 'public'. DIRECTORY_SEPARATOR . $this->config->get('theme.asset_public_path', 'dist/themes') . DIRECTORY_SEPARATOR .strtolower($theme) . DIRECTORY_SEPARATOR
        ];
    }

    private function getThemeAssetPath($theme, $key)
    {
        return 'resources/themes'. DIRECTORY_SEPARATOR . strtolower($theme) . DIRECTORY_SEPARATOR . $this->config->get('theme.directories.assets', 'assets') . DIRECTORY_SEPARATOR . $this->getAssetConfig($key);
    }

    private function getPublicAssetPath($theme, $key)
    {
        return 'public'. DIRECTORY_SEPARATOR . $this->config->get('theme.asset_public_path', 'dist/themes') . DIRECTORY_SEPARATOR .strtolower($theme) . DIRECTORY_SEPARATOR . $this->getAssetConfig($key);
    }

    /**
     * @param string|null $key
     * @return mixed
     */
    private function getAssetConfig(string $key = null)
    {
        $assets = $this->config->get('theme.assets', [
            'css'     => 'css',
            'img'     => 'img',
            'js'      => 'js',
        ]);

        if ($key) return $assets[$key];

        return $assets;
    }

}
