<?php

namespace Neputer\Supports\Console\Commands;

use Neputer\Lib\FileHandler;
use Illuminate\Routing\Route;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Contracts\Filesystem\FileNotFoundException;

class GenerateJsRouteCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:route';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate route for js';

    /**
     * @var Filesystem
     */
    private $files;

    /**
     * Create a new command instance.
     *
     * @param Filesystem $files
     */
    public function __construct(Filesystem $files)
    {
        parent::__construct();
        $this->files = $files;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     * @throws FileNotFoundException
     */
    public function handle()
    {
        $router = app('router');

        $routerList = collect($router->getRoutes())
            ->flatMap(function (Route $route) {
                return [
                    $route->getName() => '/' . $route->uri()
                ];
            });

        $fileSystem = new FileHandler();

        $content = $fileSystem->content(
            __DIR__ . DIRECTORY_SEPARATOR .'stubs'.DIRECTORY_SEPARATOR  . 'route.stub'
        );

        $replacements = [
            '{ROUTE_LIST}' => stripslashes(
                json_encode($routerList,JSON_PRETTY_PRINT)
            ),
        ];

        file_put_contents(
            resource_path('js/route.js'),
            stripslashes(str_replace(array_keys($replacements), array_values($replacements), $content))
        );

        $this->info('Route is successfully generated.');
    }

}
