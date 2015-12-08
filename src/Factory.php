<?php
/**
 * Part of the Caffeinated PHP packages.
 *
 * MIT License and copyright information bundled with this package in the LICENSE file
 */
namespace Docit\Hooks\Github;

use Docit\Support\Str;
use Docit\Core\Project;
use Docit\Hooks\Github\Contracts\Factory as FactoryContract;
use GrahamCampbell\GitHub\GitHubFactory;
use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Contracts\Queue\Queue;
use Illuminate\Filesystem\FilesystemManager;

/**
 * This is the Factory.
 *
 * @package        Docit\Hooks
 * @author         Caffeinated Dev Team
 * @copyright      Copyright (c) 2015, Caffeinated
 * @license        https://tldrlegal.com/license/mit-license MIT License
 */
class Factory implements FactoryContract
{
    protected $queue;

    protected $config;

    protected $filesystemManager;

    protected $gitHubFactory;

    protected $githubClient;

    protected $cache;

    /**
     * @param \Illuminate\Contracts\Queue\Queue        $queue
     * @param \Illuminate\Contracts\Config\Repository  $config
     * @param \Illuminate\Filesystem\FilesystemManager $filesystemManager
     * @param \GrahamCampbell\GitHub\GitHubFactory     $gitHubFactory
     * @param \Illuminate\Contracts\Cache\Repository   $cache
     */
    public function __construct(Queue $queue, Config $config, FilesystemManager $filesystemManager, GitHubFactory $gitHubFactory, Cache $cache)
    {
        $this->queue             = $queue;
        $this->config            = $config;
        $this->filesystemManager = $filesystemManager;
        $this->gitHubFactory     = $gitHubFactory;
        $this->cache             = $cache;
    }

    /**
     * githubClient
     *
     * @return \Github\Client
     */
    public function githubClient()
    {
        if (! isset($this->githubClient)) {
            $this->githubClient = $this->gitHubFactory->make([
                'method' => 'token',
                'token'  => $this->config->get('docit.hooks.github.github_token')
            ]);
        }

        return $this->githubClient;
    }

    /**
     * githubFilesystem
     *
     * @param       $repository
     * @param array $settings
     * @return \Illuminate\Contracts\Filesystem\Filesystem
     */
    public function githubFilesystem($repository, array $settings = [ ])
    {
        $settings = array_replace_recursive([
            'driver'      => 'github',
            'repository'  => $repository,
            'credentials' => [ \Potherca\Flysystem\Github\Settings::AUTHENTICATE_USING_TOKEN, $this->config->get('docit.hooks.github.github_token') ],
            'branch'      => 'master',
            'ref'         => null
        ], $settings);
        $key      = 'filesystems.disks.' . Str::slug($repository);
        $this->config->set($key, $settings);

        return $this->filesystemManager->disk($key);
    }

    /**
     * githubSync
     *
     * @param \Docit\Core\Project $project
     * @return \Docit\Hooks\Github\GitSync
     */
    public function githubSync(Project $project)
    {
        return new GitSync($project, $this->githubClient(), $project->getFiles(), $this->cache);
    }

    public function githubShow(Project $project)
    {
        return new GitShow($this, $project, $this->githubClient(), $project->getFiles(), $this->cache);
    }

    # Getters / setters

    /**
     * get queue value
     *
     * @return \Illuminate\Contracts\Queue\Queue
     */
    public function getQueue()
    {
        return $this->queue;
    }

    /**
     * Set the queue value
     *
     * @param \Illuminate\Contracts\Queue\Queue $queue
     * @return Factory
     */
    public function setQueue($queue)
    {
        $this->queue = $queue;

        return $this;
    }

    /**
     * get config value
     *
     * @return \Illuminate\Contracts\Config\Repository
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Set the config value
     *
     * @param \Illuminate\Contracts\Config\Repository $config
     * @return Factory
     */
    public function setConfig($config)
    {
        $this->config = $config;

        return $this;
    }

    /**
     * get cache value
     *
     * @return \Illuminate\Contracts\Cache\Repository
     */
    public function getCache()
    {
        return $this->cache;
    }

    /**
     * Set the cache value
     *
     * @param \Illuminate\Contracts\Cache\Repository $cache
     * @return Factory
     */
    public function setCache($cache)
    {
        $this->cache = $cache;

        return $this;
    }
}
