<?php

return [

    /*
    |--------------------------------------------------------------------------
    | PDO Fetch Style
    |--------------------------------------------------------------------------
    |
    | By default, database results will be returned as instances of the PHP
    | stdClass object; however, you may desire to retrieve records in an
    | array format for simplicity. Here you can tweak the fetch style.
    |
    */

    'fetch' => PDO::FETCH_CLASS,

    /*
    |--------------------------------------------------------------------------
    | Default Database Connection Name
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the database connections below you wish
    | to use as your default connection for all database work. Of course
    | you may use many connections at once using the Database library.
    |
    */

    'default' => env('DB_CONNECTION', 'mysql'),

    /*
    |--------------------------------------------------------------------------
    | Database Connections
    |--------------------------------------------------------------------------
    |
    | Here are each of the database connections setup for your application.
    | Of course, examples of configuring each database platform that is
    | supported by Laravel is shown below to make development simple.
    |
    |
    | All database work in Laravel is done through the PHP PDO facilities
    | so make sure you have the driver for your particular database of
    | choice installed on your machine before you begin development.
    |
    */

    'connections' => [

        'testing' => [
            'driver' => 'sqlite',
            'database' => ':memory:',
        ],

        'sqlite' => [
            'driver'   => 'sqlite',
            'database' => env('DB_DATABASE', storage_path('database.sqlite')),
            'prefix'   => env('DB_PREFIX', ''),
        ],

        'mysql' => [
            'driver'    => 'mysql',
            'host'      => env('DB_HOST', 'localhost'),
            'port'      => env('DB_PORT', 3306),
            'database'  => env('DB_DATABASE', 'forge'),
            'username'  => env('DB_USERNAME', 'forge'),
            'password'  => env('DB_PASSWORD', ''),
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => env('DB_PREFIX', ''),
            'timezone'  => env('DB_TIMEZONE','+00:00'),
            'strict'    => false,
        ],

        'db_bbs' => [
            'driver'    => 'mysql',
            'host'      => env('BBS_DB_HOST', 'localhost'),
            'port'      => env('BBS_DB_PORT', 3306),
            'database'  => env('BBS_DB_DATABASE', 'forge'),
            'username'  => env('BBS_DB_USERNAME', 'forge'),
            'password'  => env('BBS_DB_PASSWORD', ''),
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => env('BBS_DB_PREFIX', ''),
            'timezone'  => env('BBS_DB_TIMEZONE','+00:00'),
            'strict'    => false,
        ],

        'db_log' => [
            'driver'    => 'mysql',
            'host'      => env('LOG_DB_HOST', 'localhost'),
            'port'      => env('LOG_DB_PORT', 3306),
            'database'  => env('LOG_DB_DATABASE', 'forge'),
            'username'  => env('LOG_DB_USERNAME', 'forge'),
            'password'  => env('LOG_DB_PASSWORD', ''),
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => env('LOG_DB_PREFIX', ''),
            'timezone'  => env('LOG_DB_TIMEZONE','+00:00'),
            'strict'    => false,
        ],

        'db_trade' => [
            'driver'    => 'mysql',
            'host'      => env('TRADE_DB_HOST', 'localhost'),
            'port'      => env('TRADE_DB_PORT', 3306),
            'database'  => env('TRADE_DB_DATABASE', 'forge'),
            'username'  => env('TRADE_DB_USERNAME', 'forge'),
            'password'  => env('TRADE_DB_PASSWORD', ''),
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => env('TRADE_DB_PREFIX', ''),
            'timezone'  => env('TRADE_DB_TIMEZONE','+00:00'),
            'strict'    => false,
        ],


        'db_ds' => [
            'driver'    => 'mysql',
            'host'      => env('DS_HOST', 'localhost'),
            'port'      => env('DS_PORT', 3306),
            'database'  => env('DS_DATABASE', 'forge'),
            'username'  => env('DS_USERNAME', 'forge'),
            'password'  => env('DS_PASSWORD', ''),
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => env('DS_PREFIX', ''),
            'timezone'  => env('DS_TIMEZONE','+00:00'),
            'strict'    => false,
        ],

        'db_qz' => [
            'driver'    => 'mysql',
            'host'      => env('QZ_HOST', 'localhost'),
            'port'      => env('QZ_PORT', 3306),
            'database'  => env('QZ_DATABASE', 'forge'),
            'username'  => env('QZ_USERNAME', 'forge'),
            'password'  => env('QZ_PASSWORD', ''),
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => env('QZ_PREFIX', ''),
            'timezone'  => env('QZ_TIMEZONE','+00:00'),
            'strict'    => false,
        ],

        'pgsql' => [
            'driver'   => 'pgsql',
            'host'     => env('DB_HOST', 'localhost'),
            'port'     => env('DB_PORT', 5432),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'charset'  => 'utf8',
            'prefix'   => env('DB_PREFIX', ''),
            'schema'   => 'public',
        ],

        'sqlsrv' => [
            'driver'   => 'sqlsrv',
            'host'     => env('DB_HOST', 'localhost'),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'prefix'   => env('DB_PREFIX', ''),
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Migration Repository Table
    |--------------------------------------------------------------------------
    |
    | This table keeps track of all the migrations that have already run for
    | your application. Using this information, we can determine which of
    | the migrations on disk haven't actually been run in the database.
    |
    */

    'migrations' => 'migrations',

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

        'cluster' => env('REDIS_CLUSTER', false),

        'default' => [
            'host'     => env('REDIS_HOST', '127.0.0.1'),
            'port'     => env('REDIS_PORT', 6379),
            'database' => env('REDIS_DATABASE', 0),
            'password' => env('REDIS_PASSWORD', null)
        ],

        'cache' => [
            'host'     => env('REDIS_HOST', '127.0.0.1'),
            'port'     => env('REDIS_PORT', 6379),
            'database' => env('REDIS_DATABASE', 0),
            'password' => env('REDIS_PASSWORD', null),
            'database' => 1
        ]

    ],

];
