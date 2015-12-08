<?php

return [
    'github_token'           => env('GITHUB_TOKEN', null),
    'default_project_config' => [
        'enable_github_hook'   => false,
        'github_hook_settings' => [
            'owner'      => '',
            'repository' => '',
            'show'       => [
                'issues'       => [
                    'enabled' => false,
                    'state'   => 'open',
                    'sort'    => 'created'
                ],
                'stars'        => false,
                'forks'        => false,
                'fork_me_link' => false
            ],
            'sync'       => [
                'enabled'        => false,
                'webhook_secret' => env('DOCIT_PROJECT_GITHUB_WEBHOOK_SECRET', null),
                'sync'           => [
                    'branches' => [ 'master' ],
                    /**
                     * Version range expression
                     *
                     * @var string
                     * @see  \vierbergenlars\SemVer\expression
                     * @link https://github.com/vierbergenlars/php-semver
                     */
                    'versions' => '1.x || >=2.5.0 || 5.0.0 - 7.2.3'
                ],
                'paths'          => [
                    'docs' => 'docs',
                    'menu' => 'docs/menu.yml'
                ]
            ]
        ]
    ]
];
