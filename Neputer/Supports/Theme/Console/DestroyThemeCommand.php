<?php

namespace Neputer\Supports\Theme\Console;

use Throwable;
use Illuminate\Console\Command;
use Neputer\Supports\Theme\Theme;
use Illuminate\Filesystem\Filesystem;
use Exception as ThemeNotFoundException;
use Exception as ThemeNotDeletedException;
use Neputer\Supports\Theme\Engines\ThemeScanner;

/**
 * Class DestroyThemeCommand
 * @package Neputer\Supports\Theme\Console
 */
final class DestroyThemeCommand extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'theme:destroy {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete the given theme';

    /**
     * @var ThemeScanner
     */
    private $scanner;

    /**
     * DestroyThemeCommand constructor.
     *
     * @param ThemeScanner $themeScanner
     */
    public function __construct( ThemeScanner $themeScanner )
    {
        parent::__construct();
        $this->scanner = $themeScanner;
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

        try {

            throw_if(
                !$this->scanner->isThemeAvailable($selectedTheme),
                new ThemeNotFoundException(ucfirst($selectedTheme) . ' is not found.')
            );

            throw_if(
                strtolower(Theme::active()) === strtolower($selectedTheme),
                new ThemeNotDeletedException(ucfirst($selectedTheme) . ' cannot be deleted. It\'s active. Please activate other and delete the theme.')
            );

        } catch (\Exception $exception) {
            $this->info($exception->getMessage());
            exit();
        }

        (new Filesystem)->deleteDirectory(theme_path($selectedTheme));
        (new Filesystem)->deleteDirectory(public_path(config('theme.asset_public_path', 'dist/themes').DIRECTORY_SEPARATOR.$selectedTheme));
        $this->info(ucfirst($selectedTheme) . ' is deleted successfully !');
    }

}
