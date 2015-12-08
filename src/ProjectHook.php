<?php
/**
 * Part of the Caffeinated PHP packages.
 *
 * MIT License and copyright information bundled with this package in the LICENSE file
 */
namespace Docit\Hooks\Github;

use Docit\Core\Contracts\Hook;
use Docit\Core\Project;

/**
 * This is the Hook.
 *
 * @package        Docit\Core
 * @author         Caffeinated Dev Team
 * @copyright      Copyright (c) 2015, Caffeinated
 * @license        https://tldrlegal.com/license/mit-license MIT License
 */
class ProjectHook implements Hook
{


    /**
     * handle
     *
     * @param \Docit\Core\Project $project
     */
    public function handle(Project $project)
    {
        $that = $this;
        // Add a method on the project class that creates a new GitSync for that specific project
        Project::macro('githubSync', function ()
        {
            /** @var Project $this */
            return app('docit.hooks.github.factory')->githubSync($this);
        });

        // Add a method on the project class that creates a new GitSync for that specific project
        Project::macro('githubShow', function () use ($that)
        {
            /** @var Project $this */
            return app('docit.hooks.github.factory')->githubShow($this);
        });
    }
}
