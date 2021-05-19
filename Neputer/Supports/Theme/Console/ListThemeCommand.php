<?php

namespace Neputer\Supports\Theme\Console;

use Throwable;
use Illuminate\Console\Command;
use Illuminate\Config\Repository;
use Illuminate\Support\Collection;
use Neputer\Supports\Theme\Engines\ThemeScanner;

/**
 * Class ListThemeCommand
 * @package Neputer\Supports\Theme\Console
 */
final class ListThemeCommand extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'theme:list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all available themes with their information';

    /**
     * @var ThemeScanner
     */
    private $scanner;

    /**
     * @var Repository
     */
    private $config;

    /**
     * EditThemeCommand constructor.
     *
     * @param ThemeScanner $themeScanner
     * @param Repository $configRepository
     */
    public function __construct( ThemeScanner $themeScanner, Repository $configRepository )
    {
        parent::__construct();
        $this->scanner = $themeScanner;
        $this->config  = $configRepository;
    }

    /**
     * Execute the console command.
     *
     * @return void
     * @throws Throwable
     */
    public function handle()
    {
        $this->makeTable();
    }

    /**
     * Return table headers
     *
     * @return array
     */
    private function headers()
    {
        return [ 'Theme', 'Author', 'Version', 'Type', 'Keywords', 'Priority', 'Status', ];
    }

    /**
     * @throws Throwable
     */
    private function makeTable()
    {
        $rows = [];

        foreach ($this->getFiles() as $file => $info) {
            $rows[] = array_combine($this->headers(), [
                $this->resolveThemeName($info),
                $this->resolveProperty($info, 'author'),
                $this->resolveProperty($info, 'version'),
                $this->resolveProperty($info, 'type'),
                $this->resolveProperty($info, 'keywords'),
                $this->resolveProperty($info, 'priority'),
                $this->resolveProperty($info, 'active'),
            ]);
        }

        $this->table($this->headers(), $rows);
    }

    /**
     * @param array $themeInfo
     * @return string
     */
    private function resolveThemeName(array $themeInfo)
    {
        $alias = '';

        if (array_key_exists('alias', $themeInfo)) {
            $alias = ' ('.$themeInfo['alias'].')';
        }
        return $this->resolveProperty($themeInfo, 'name') . $alias;
    }

    /**
     * @return Collection
     * @throws Throwable
     */
    private function getFiles()
    {
        return $this->scanner->get();
    }

    /**
     * Resolve the property value for the given theme info key
     *
     * @param array $args
     * @param string $key
     * @param string $undefined
     * @return mixed|string
     */
    private function resolveProperty(array $args, string $key, string $undefined = 'N/A')
    {
        if (! isset($args[$key])) {
            return $undefined;
        } else if (is_array($args[$key])) {
            return implode(',', $args[$key]);
        }

        return $args[$key];
    }

}
