<?php
/**
* Part of the Caffeinated PHP packages.
*
* MIT License and copyright information bundled with this package in the LICENSE file
 */
namespace Docit\Hooks\Github\Http\Controllers;

use Docit\Core\Contracts\Factory;
use Docit\Core\Contracts\Menus\MenuFactory;
use Docit\Core\Http\Controllers\Controller;
use Illuminate\Contracts\Queue\Queue;
use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Http\Request;

/**
 * This is the GithubController.
 *
 * @package        Docit\Hooks
 * @author         Caffeinated Dev Team
 * @copyright      Copyright (c) 2015, Caffeinated
 * @license        https://tldrlegal.com/license/mit-license MIT License
 */
class GithubController extends Controller
{
    /**
     * @var \Illuminate\Http\Request
     */
    protected $request;

    /**
     * @var \Illuminate\Contracts\Queue\Queue
     */
    protected $queue;

    /**
     * @param \Docit\Core\Factory           $factory
     * @param \Docit\Core\Contracts\Menus\MenuFactory $menus
     * @param \Illuminate\Contracts\View\Factory       $view
     * @param \Illuminate\Http\Request                 $request
     * @param \Illuminate\Contracts\Queue\Queue        $queue
     */
    public function __construct(Factory $factory, MenuFactory $menus, ViewFactory $view, Request $request, Queue $queue)
    {
        parent::__construct($factory, $menus, $view);
        $this->request = $request;
        $this->queue = $queue;
    }

    /**
     * webhook
     *
     * @param $type
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function webhook()
    {

        $headers = [
            'delivery'   => $this->request->header('x-github-delivery'),
            'event'      => $this->request->header('x-github-event'),
            'user-agent' => $this->request->header('user-agent'),
            'signature'  => $this->request->header('x-hub-signature')
        ];
        $payload = $this->request->all();
        $repo    = strtolower($payload[ 'repository' ][ 'full_name' ]);

        foreach ($this->factory->getProjects() as $project) {
            if ( $project->config('enable_github_hook', false) === false || $project->config('github_hook_settings.sync.enabled', false) === false ) {
                continue;
            }

            $config      = $project->config('github_hook_settings');
            $projectRepo = $project->config('github_hook_settings.owner') . '/' . $project->config('github_hook_settings.repository');

            if ($repo !== $projectRepo) {
                continue;
            }

            $hash = hash_hmac('sha1', file_get_contents("php://input"), $project->config('github_hook_settings.sync.webhook_secret'));


            if ($headers[ 'signature' ] === "sha1=$hash") {
                $this->queue->push(\Docit\Hooks\Github\Commands\GithubSyncProject::class, [ 'project' => $project->getName() ]);
                return response('', 200);
            } else {
                return response('Invalid hash', 403);
            }
        }

        return response('', 500);
    }
}
