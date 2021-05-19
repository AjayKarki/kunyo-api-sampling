<?php

return [

    'path' => theme_path(),

    'asset_public_path' => 'dist/themes',

    'lang' => [
        'en', 'np',
    ],

    'directories' => [
        'assets'  => 'assets',
        'lang'    => 'lang',
        'view'    => 'views',
    ],

    'assets'      => [
        'css'     => 'css',
        'img'     => 'img',
        'js'      => 'js',
    ],

    'custom'      => [
        'views/shared',
        'views/layouts',
        'views/pages/widgets/components',
        'views/pages/widgets/templates',
    ],

];
