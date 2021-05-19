<?php

namespace Neputer\Supports\Theme\Console;

use Throwable;
use Illuminate\Console\Command;
use Exception as ThemeNotFoundException;
use Neputer\Supports\Theme\Engines\ThemeEditor;
use Neputer\Supports\Theme\Engines\ThemeScanner;
use Illuminate\Contracts\Filesystem\FileNotFoundException;

/**
 * Class EditThemeCommand
 * @package Neputer\Supports\Theme\Console
 */
final class EditThemeCommand extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'theme:edit {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Edit a theme properties';

    /**
     * @var ThemeScanner
     */
    private $scanner;

    /**
     * Information of new theme info
     *
     * @var $theme
     */
    private $theme = [];

    /**
     * Theme path of going to be updated theme
     *
     * @var $themePath
     */
    private $themePath;

    /**
     * The instance of scanned theme
     *
     * @var $scannedTheme
     */
    private $scannedTheme;

    /**
     * The symbol to be recognized ie means it's an array
     *
     * @var string
     */
    private $symbol = '*';

    /**
     * List of questions to be asked
     *
     * @var array
     */
    private $questions = [
        'author'      => 'Enter the name of the author for the theme ?',
        'email'       => 'Enter the email of the author for the theme ?',
        'description' => 'Enter the description for the theme ?',
        'keywords'    => 'Enter the %s separated keywords for the theme ?',
        'active'      => 'Enter the status of the theme ? [ 0/1, 0 = inactive / 1 = active ] ?',
        'priority'    => 'Enter the priority for the theme ? [ From 0 to 100 ]',
        'version'     => 'Enter the semantic version for the system ? [ 0.1.0 ]'
    ];

    /**
     * EditThemeCommand constructor.
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
        $selectedTheme = $this->argument('name');

        try {

            throw_if(
                !$this->scanner->isThemeAvailable($selectedTheme),
                new ThemeNotFoundException(ucfirst($selectedTheme) . ' is not found.')
            );

        } catch (\Exception $exception) {
            $this->info($exception->getMessage());
            exit();
        }

        // @TODO issue
        $this->themePath = str_replace('*', strtolower($selectedTheme),
            $this->scanner->getThemePath($this->scanner::THEME_KEY, $selectedTheme));

        $this->askQuestions();

        ThemeEditor::update($this->themePath, $this->resolveAnswers());

        $this->info(ucfirst($selectedTheme).' theme is updated successfully !');
    }

    /**
     * @throws FileNotFoundException
     */
    private function askQuestions()
    {
        foreach ($this->questions as $key => $question) {

            $default = $this->resolveDefault($this->scanner->retrieveThemeInfo($this->themePath, $key));

            $this->theme[$key] = $this->getAnswer($question, $default, $key);
        }
    }

    /**
     * Return the answer
     *
     * @param $question
     * @param $default
     * @param $key
     * @return array|mixed
     */
    private function getAnswer($question, $default, $key)
    {
        $answer = function ($question, $default, $key) {

            $answer = $this->ask(sprintf($question, $this->symbol) . " ({$default})", $default);

            if ($key === 'name') {
                $this->scanner->isThemeAvailable($answer);
            }

            if (strpos($answer, $this->symbol) !== false) {
                return explode($this->symbol, $answer);
            }

            return $answer;
        };

        return $answer($question, $default, $key);
    }

    /**
     * Resolving the answers for insertion
     *
     * @return array
     * @throws FileNotFoundException
     */
    private function resolveAnswers()
    {
        $newThemeInfo = [];

        foreach ($this->scanner->retrieveThemeInfo($this->themePath) as $key => $value) :

            $newThemeInfo += [ $key  => !array_key_exists($key, $this->theme) ? $value : $this->theme[$key] ];

        endforeach;

        return $newThemeInfo;
    }

    /**
     * Resolving the default value for the theme
     *
     * @param $value
     * @return string
     */
    private function resolveDefault($value)
    {
        switch (true) {
            case $value == null || empty($value):
                $value = 'Undefined';
                break;
            case is_array($value):
                $value = implode(',', $value);
                break;
            default:
                return $value;
        }
        return $value;
    }

}
