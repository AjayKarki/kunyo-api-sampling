<?php

namespace Neputer\Supports\Access;

use Exception;
use Illuminate\Routing\Router;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Config\Repository as Config;

/**
 * Class GeneratePermissionCommand
 * @package Neputer\Supports\Access
 */
class GeneratePermissionCommand extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'access:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate permissions';

    /**
     * The FileSystem instance
     *
     * @var $file
     */
    private $file;

    /**
     * The Config repository instance
     *
     * @var $config
     */
    private $config;

    /**
     * The Config repository instance
     *
     * @var $router
     */
    private $router;

    # List of ignored access routes
    const IGNORED_ACCESS = [
        null,
        'debugbar.openhandler',
        'debugbar.clockwork',
        'debugbar.telescope',
        'debugbar.assets.css',
        'debugbar.assets.js',
        'debugbar.cache.delete',
        'unisharp.lfm.show',
        'unisharp.lfm.getCrop',
        'unisharp.lfm.getCropimage',
        'unisharp.lfm.getDelete',
        'unisharp.lfm.',
        'unisharp.lfm.domove',
        'unisharp.lfm.performResize',
        'unisharp.lfm.getDownload',
        'unisharp.lfm.getErrors',
        'unisharp.lfm.getFolders',
        'unisharp.lfm.getItems',
        'unisharp.lfm.move',
        'unisharp.lfm.getAddfolder',
        'unisharp.lfm.getRename',
        'unisharp.lfm.getResize',
        'unisharp.lfm.upload',
        'telescope',
    ];

    # Character to be used to glue permission
    const GLUE = '_';

    # Characters to be black listed
    const BLACK_LISTED_CHARS = [ '.', ];

    # This file name will be used to cache the generated permissions
    const PERMISSION_FILE_NAME = 'permissions.php';

    /**
     * GeneratePermissionCommand constructor.
     *
     * @param Filesystem $file
     * @param Config $config
     * @param Router $router
     */
    public function __construct(Filesystem $file, Config $config, Router $router)
    {
        parent::__construct();
        $this->file   = $file;
        $this->config = $config;
        $this->router = $router;
    }

    /**
     * Execute the console command.
     *
     * @throws Exception
     */
    public function handle()
    {
        $this->info('Permission generator is started ... !!!');

        $permissions = $this->cachePermissions($this->retrievePermissions());
        $this->savePermissions($permissions);

        $this->info('Successfully generated !!!');
    }

    /**
     * Cache and return permissions
     *
     * @param array $permissions
     * @return array
     * @throws Exception
     */
    private function cachePermissions(array $permissions)
    {
        $cachePath = base_path('bootstrap') . DIRECTORY_SEPARATOR . 'cache';

        if ( ! $this->file->exists($cachePath) ) {
            $this->file->makeDirectory($cachePath, 0755, true, true);
        }

        $file = $cachePath . DIRECTORY_SEPARATOR . static::PERMISSION_FILE_NAME;

        if (! is_writable(dirname($file))) {
            throw new Exception('The '.dirname($file).' directory must be present and writable.');
        }

        $this->file->replace($file, '<?php'. PHP_EOL . PHP_EOL .'return ' . var_export($permissions, true).';');
        return $permissions;
    }

    /**
     * Save permissions
     *
     * @param array $permissions
     */
    private function savePermissions(array $permissions)
    {
        foreach ($permissions as $permission) :

            $description = $this->resolvePermissionDescription($permission);

            app($this->config->get('permission.table'))->updateOrCreate(
                [ 'slug' => $permission, ],
                [ 'name' => $description, 'slug' => $permission, 'description' => $description, ]
            );

        endforeach;
    }

    /**
     * Return resolved permission slug
     *
     * @param string $route
     * @return string|string[]
     */
    private function resolvePermissionSlug(string $route)
    {
        return str_replace($this->getBlackListedCharacters(), static::GLUE, $route);
    }

    /**
     * Return resolved permission description
     *
     * @param string $route
     * @return string|string[]
     */
    private function resolvePermissionDescription(string $route)
    {
        return ucwords(str_replace(static::GLUE, ' ', $route));
    }

    /**
     * Return permissions
     *
     * @return array
     */
    private function retrievePermissions()
    {
        $permissions = [];

        $ignoredRoutes = $this->getIgnoredRoutes();

        foreach ($this->router->getRoutes() as $route) :

            if (!in_array($route->getName(), $ignoredRoutes)) {
                $permissions[] = $this->resolvePermissionSlug($route->getName());
            }

        endforeach;

        return $permissions;
    }

    /**
     * Return ignored routes
     *
     * @return array
     */
    private function getIgnoredRoutes()
    {
        return array_merge(self::IGNORED_ACCESS, $this->config->get('permission.ignored_routes', []));
    }

    /**
     * Return black listed characters
     *
     * This characters will be replaced using constant glue while generating permission
     *
     * @return array
     */
    private function getBlackListedCharacters()
    {
        return $this->config->get('permission.black-listed-chars', self::BLACK_LISTED_CHARS);
    }

    private function disableRoutes()
    {
        $this->config->set('app.environment', 'production');
    }

}
