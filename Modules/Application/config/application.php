<?php

return [

    'front_endpoint' => 'http://localhost:3000/',

    'perPage' => 25,

    'routes' => [
        'namespace'    => 'Modules\Application\Http\Controllers',
        'middleware'   => [ 'api', ],
        'prefix'       => 'api',
    ],

];
