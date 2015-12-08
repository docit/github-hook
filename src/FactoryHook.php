<?php
/**
 * Part of the Caffeinated PHP packages.
 *
 * MIT License and copyright information bundled with this package in the LICENSE file
 */
namespace Docit\Hooks\Github;

use Docit\Core\Contracts\Hook;
use Docit\Core\Factory as DocitFactory;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Filesystem\Filesystem;

/**
 * This is the Hook.
 *
 * @package        Docit\Core
 * @author         Caffeinated Dev Team
 * @copyright      Copyright (c) 2015, Caffeinated
 * @license        https://tldrlegal.com/license/mit-license MIT License
 */
class FactoryHook implements Hook
{

    /**
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * @var \Illuminate\Contracts\Config\Repository
     */
    protected $config;

    /**
     * @param \Illuminate\Filesystem\Filesystem       $files
     * @param \Illuminate\Contracts\Config\Repository $config
     */
    public function __construct(Filesystem $files, Repository $config)
    {
        $this->files  = $files;
        $this->config = $config;
    }

    public function handle(DocitFactory $docit)
    {
        $docit->setConfig(
            array_replace_recursive(
                $docit->config(),
                $this->config->get('docit.hooks.github.default_project_config')
            )
        );
    }
}
