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
        /*
        'rest/api/all/{layout}',
        'rest/api/get/{layout}/{record_id}',
        'rest/api/edit/{layout}/{record_id}',
        'rest/api/find/{layout}',
        'rest/api/post/{layout}/{record_id}',
        'rest/api/save/{layout}/{record_id}',
        'rest/api/store/{layout}/{record_id}',
        'rest/api/put/{layout}/{record_id}',
        'rest/api/update/{layout}/{record_id}',
        'rest/api/delete/{layout}/{record_id}',
        */
    ];
}
