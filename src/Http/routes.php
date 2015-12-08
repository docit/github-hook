<?php



    Route::any('github-sync-webhook/{type}', [
        'as'   => 'docit.github-sync-webhook',
        'uses' => 'GithubController@webhook'
    ]);
