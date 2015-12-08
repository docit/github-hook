<?php
/**
 * Part of Robin Radic's PHP packages.
 *
 * MIT License and copyright information bundled with this package
 * in the LICENSE file or visit http://radic.mit-license.org
 */
namespace Docit\Hooks\Github\Console;

use Docit\Support\Command;
use Docit\Core\Contracts\Factory;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Illuminate\Contracts\Queue\Queue;

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
class GithubSyncCommand extends Command
{
    use DispatchesJobs;

    protected $signature = 'docit:github:sync {--queue= : asdf}';

    protected $description = 'Synchronise all Github projects.';

    /** @var \Docit\Core\Factory */
    protected $factory;

    protected $queue;

    public function __construct(Factory $factory, Queue $queue)
    {
        parent::__construct();
        $this->factory = $factory;
        $this->queue = $queue;
    }

    public function handle()
    {

        $githubProjects = [ ];
        $choices        = [ ];
        foreach ($this->factory->getProjects() as $project) {
            if ($project->config('enable_github_hook', false) && $project->config('github_hook_settings.sync.enabled', false)) {
                $githubProjects[] = $project;
                $choices[]        = $project->getName();
            }
        }
        $project = $this->choice('Pick the github enabled project you wish to sync', $choices);



        if ($this->option('queue')) {
            $this->queue->push(\Docit\Hooks\Github\Commands\GithubSyncProject::class, [ 'project' => $project ]);
        } else {
            $project = $this->factory->getProject($project);
            $total = count($project->githubSync()->getBranchesToSync()) + count($project->githubSync()->getVersionsToSync());
            if ($total === 0) {
                $this->info('Nothing to sync. Everything is up-to-date');

                return;
            }
            $this->output->progressStart($total);
            $that = $this;
            $project->githubSync()->syncWithProgress(function ($current) use ($that) {

                $that->output->progressAdvance();
            });
            $this->output->progressFinish();
            $this->info('Synchronised ' . $total . ' versions/branches');
        }
    }

}
