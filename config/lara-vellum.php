<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Enabled
    |--------------------------------------------------------------------------
    |
    | Master switch for the package. When disabled, the package registers
    | nothing beyond its configuration.
    |
    */

    'enabled' => env('LARA_VELLUM_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Routing
    |--------------------------------------------------------------------------
    |
    | The public reading site and the /vellum dashboard are mounted under these
    | prefixes. The middleware groups wrap the published pages and the
    | authenticated dashboard respectively.
    |
    */

    'routing' => [
        'public' => [
            'prefix' => env('LARA_VELLUM_PUBLIC_PREFIX', ''),
            'middleware' => ['web'],
        ],

        'dashboard' => [
            'prefix' => env('LARA_VELLUM_DASHBOARD_PREFIX', 'vellum'),
            'middleware' => ['web'],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Authorization
    |--------------------------------------------------------------------------
    |
    | The dashboard defers to the host application's auth. Requests must pass
    | the gate below to reach /vellum. By default the gate simply requires an
    | authenticated user; publish this config and redefine the gate (or point
    | it at your own ability) to restrict access to editors/admins.
    |
    */

    'authorization' => [
        'gate' => env('LARA_VELLUM_GATE', 'viewVellum'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Persistence
    |--------------------------------------------------------------------------
    |
    | The table backing Vellum posts. Namespaced by default to avoid clashing
    | with the host application's own tables.
    |
    */

    'database' => [
        'posts_table' => env('LARA_VELLUM_POSTS_TABLE', 'vellum_posts'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Static rendering
    |--------------------------------------------------------------------------
    |
    | On publish, a post's Markdown is rendered to a full HTML page and written
    | to a content-hashed file on the disk below. Public reads stream that file
    | for static-site speed. Point "disk" at a public disk (and run
    | `php artisan storage:link`) to let your web server serve pages without
    | booting PHP at all.
    |
    */

    'static' => [
        'disk' => env('LARA_VELLUM_STATIC_DISK', 'local'),
        'path' => env('LARA_VELLUM_STATIC_PATH', 'vellum/pages'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Views
    |--------------------------------------------------------------------------
    |
    | The Blade views used to render the public reading experience. Publish the
    | package views (tag: lara-vellum-views) and edit these, or point them at
    | your own templates, to theme the site.
    |
    */

    'views' => [
        'public_show' => env('LARA_VELLUM_VIEW_PUBLIC_SHOW', 'lara-vellum::public.show'),
        'public_index' => env('LARA_VELLUM_VIEW_PUBLIC_INDEX', 'lara-vellum::public.index'),
    ],

];
