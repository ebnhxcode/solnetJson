<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        '/rest/api/post/{layout}',
        '/rest/api/post/{layout}',
        '/rest/api/post/layout',
        '/rest/api/post/',

        'rest/api/all/{layout}',
        'rest/api/get/{layout}/{record_id}',
        'rest/api/edit',
        'rest/api/find',
        'rest/api/post',
        'rest/api/save',
        'rest/api/store',
        'rest/api/put',
        'rest/api/update',
        'rest/api/delete',
    ];
}
