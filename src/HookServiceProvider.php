<?php
/**
 * Part of the Caffeinated PHP packages.
 *
 * MIT License and copyright information bundled with this package in the LICENSE file
 */
namespace Docit\Hooks\Github;

use Docit\Support\ServiceProvider;
use Docit\Core\Traits\DocitProviderTrait;
use Docit\Hooks\Github\Console\GithubSyncCommand;
use Docit\Hooks\Github\Console\GithubTestCommand;
use Github\Client;
use Illuminate\Contracts\Foundation\Application;
use League\Flysystem\Filesystem;
use Potherca\Flysystem\Github\Api;
use Potherca\Flysystem\Github\GithubAdapter;
use Potherca\Flysystem\Github\Settings;

/**
 * This is the GithubServiceProvider.
 *
 * @package        Docit\Core
 * @author         Caffeinated Dev Team
 * @copyright      Copyright (c) 2015, Caffeinated
 * @license        https://tldrlegal.com/license/mit-license MIT License
 */
class HookServiceProvider extends ServiceProvider
{
    use DocitProviderTrait;

    protected $dir = __DIR__;

    protected $configFiles = ['docit.hooks.github'];

    protected $providers = [
        \GrahamCampbell\GitHub\GitHubServiceProvider::class,
        Providers\RouteServiceProvider::class
    ];

    protected $commands = [
        GithubSyncCommand::class,
        GithubTestCommand::class
    ];

    protected $singletons = [
        'docit.hooks.github.factory' => Factory::class
    ];

    /**
     * Collection of aliases.
     *
     * @var array
     */
    protected $aliases = [
        'docit.hooks.github.factory' => Contracts\Factory::class
    ];


    public function boot()
    {
        $app = parent::boot();
    }


    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $app = parent::register();

        $this->registerGithubFilesystem();

        $this->addRouteProjectNameExclusions('github-sync-webhook');

        // Add the hook which merges the docit config.
        $this->addDocitHook('factory:ready', FactoryHook::class);

        // And add the hook providing the  `github` method for projects to retreive a gitsync instance for that specific project
        $this->addDocitHook('project:ready', ProjectHook::class);

    }

    protected function registerGithubFilesystem()
    {
        $fsm = $this->app->make('filesystem');
        $fsm->extend('github', function (Application $app, $config) {
            $settings = new Settings($config['repository'], $config['credentials'], $config['branch'], $config['ref']);
            $api = new Api(new Client(), $settings);
            $adapter = new GithubAdapter($api);
            return new Filesystem($adapter);
        });
    }
}
