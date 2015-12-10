<?php
/**
 * Part of the Caffeinated PHP packages.
 *
 * MIT License and copyright information bundled with this package in the LICENSE file
 */

namespace Docit\Hooks\Github\Commands;

use Docit\Core\Contracts\Factory;
use Illuminate\Contracts\Queue\Job;

/**
 * This is the DocitSyncGithubProject.
 *
 * @package        Docit\Core
 * @author         Caffeinated Dev Team
 * @copyright      Copyright (c) 2015, Caffeinated
 * @license        https://tldrlegal.com/license/mit-license MIT License
 */
class GithubSyncProject
{
    protected $factory;

    /**
     * @param \Docit\Core\Factory $factory
     */
    public function __construct(Factory $factory)
    {
        $this->factory = $factory;
    }

    public function fire(Job $job, $data)
    {
        if($job->attempts() > 2){
            $job->delete();
        }
        $this->factory->getProject($data[ 'project' ])->githubSync()->syncAll();
    }
}
