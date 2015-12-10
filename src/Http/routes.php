<?php


Route::post('github-sync-webhook', [
    'as'   => 'docit.github-sync-webhook',
    'uses' => 'GithubController@webhook'
]);
