<?php

return [

    'elasticsearch' => [
        'index' => 'lighthouse',
        'type'  => 'torrent',
    ],

    /*
    |--------------------------------------------------------------------------
    | Redis Databases
    |--------------------------------------------------------------------------
    |
    | Redis is an open source, fast, and advanced key-value store that also
    | provides a richer set of commands than a typical key-value systems
    | such as APC or Memcached. Laravel makes it easy to dig right in.
    |
    */

    'redis' => [

        'cluster' => false,

        'default' => [
            'host'     => 'redis',
            'port'     => 6379,
            'database' => 0,
        ],

    ],

];
