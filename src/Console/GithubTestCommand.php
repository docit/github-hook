<?php
/**
 * Part of Robin Radic's PHP packages.
 *
 * MIT License and copyright information bundled with this package
 * in the LICENSE file or visit http://radic.mit-license.org
 */
namespace Docit\Hooks\Github\Console;

use Docit\Support\Command;
use Docit\Hooks\Github\Contracts\Factory as GithubFactory;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Docit\Core\Contracts\Factory as Docit;

/**
 * This is the CoreListCommand class.
 *
 * @package                   Docit\Core
 * @version                   1.0.0
 * @author                    Robin Radic
 * @license                   MIT License
 * @copyright                 2015, Robin Radic
 * @link                      https://github.com/robinradic
 */
class GithubTestCommand extends Command
{
    use DispatchesJobs;

    protected $signature = 'docit:github:test';

    protected $description = 'Synchronise all Github projects.';

    /**
     * @var \Docit\Hooks\Github\Factory
     */
    protected $factory;

    /**
     * @var \Docit\Core\Factory
     */
    protected $docit;


    /**
     * @param \Docit\Hooks\Github\Contracts\Factory $factory
     * @param \Docit\Core\Contracts\Factory        $docit
     */
    public function __construct(GithubFactory $factory, Docit $docit)
    {
        parent::__construct();
        $this->factory = $factory;
        $this->docit = $docit;
    }


    public function handle()
    {
        $show = $this->factory->githubShow($this->docit->getProject('caffeinated-beverage'));
        $issues = $show->getIssues();
        $stats = $show->getStars();
        $a = 'a';
    }
}
