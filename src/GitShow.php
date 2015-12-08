<?php
/**
 * Part of the Caffeinated PHP packages.
 *
 * MIT License and copyright information bundled with this package in the LICENSE file
 */
namespace Docit\Hooks\Github;

use Docit\Core\Project;
use Docit\Hooks\Github\Contracts\Factory as GithubFactory;
use Github\Client;
use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Contracts\Filesystem\Filesystem;

/**
 * This is the GitShow.
 *
 * @package        Docit\Hooks
 * @author         Caffeinated Dev Team
 * @copyright      Copyright (c) 2015, Caffeinated
 * @license        https://tldrlegal.com/license/mit-license MIT License
 */
class GitShow
{

    /**
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * @var \Github\Client
     */
    protected $github;

    /**
     * @var \Docit\Core\Project
     */
    protected $project;

    /**
     * @var \Illuminate\Contracts\Cache\Repository
     */
    protected $cache;

    protected $factory;

    /**
     * @param \Docit\Core\Project                   $project
     * @param \Github\Client                         $github
     * @param \Illuminate\Filesystem\Filesystem      $files
     * @param \Illuminate\Contracts\Cache\Repository $cache
     */
    public function __construct(GithubFactory $factory, Project $project, Client $github, Filesystem $files, Cache $cache)
    {
        $this->factory = $factory;
        $this->project = $project;
        $this->github  = $github;
        $this->files   = $files;
        $this->cache   = $cache;
    }

    public function setting($key, $default = null)
    {
        return array_get($this->project->config('github_hook_settings'), $key, $default);
    }

    public function getIssues()
    {
        return $this->github->issues()->all(
            $this->setting('owner'),
            $this->setting('repository'),
            [
                'state' => $this->setting('show.issues.state'),
                'sort'  => $this->setting('show.issues.sort')
            ]
        );
    }

    public function getStars()
    {

        $owner                 = $this->setting('owner');
        $repo                  = $this->setting('repository');
        $data                  = [ ];
        $data[ 'subscribers' ] = $this->github->repos()->subscribers($owner, $repo);
        $data[ 'issues' ]      = $this->github->issues()->all($owner, $repo, [
            'state' => $this->setting('show.issues.state'),
            'sort'  => $this->setting('show.issues.sort')
        ]);
        $data[ 'releases' ]    = $this->github->repos()->releases()->all($owner, $repo);
        $data[ 'downloads' ]   = $this->github->repos()->downloads()->all($owner, $repo);
        $data[ 'forks' ] = $this->github->repos()->forks()->all($owner, $repo);

        return $data;
    }
}
