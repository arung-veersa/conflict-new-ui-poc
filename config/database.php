<?php

use Illuminate\Support\Str;

return [

    /*
    |--------------------------------------------------------------------------
    | Default Database Connection Name
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the database connections below you wish
    | to use as your default connection for database operations. This is
    | the connection which will be utilized unless another connection
    | is explicitly specified when you execute a query / statement.
    |
    */

    'default' => env('DB_CONNECTION', 'snowflake'),

    /*
    |--------------------------------------------------------------------------
    | Database Connections
    |--------------------------------------------------------------------------
    |
    | Below are all of the database connections defined for your application.
    | Since this project uses Snowflake via PHP PDO ODBC driver, only the
    | Snowflake connection is configured.
    |
    */

    'connections' => [

        'snowflake' => [
            'driver' => 'odbc',
            'dsn' => env('SNOWFLAKE_ODBC_DSN', 'SnowflakeDSIIDriver'),
            'host' => env('SNOWFLAKE_ACCOUNT', 'IKB38126') . '.us-east-1.snowflakecomputing.com',
            'port' => env('SNOWFLAKE_PORT', '443'),
            'database' => env('SNOWFLAKE_DATABASE', 'CONFLICTREPORT_SANDBOX'),
            'schema' => env('SNOWFLAKE_SCHEMA', 'PUBLIC'),
            'warehouse' => env('SNOWFLAKE_WAREHOUSE', 'CONFLICTREPORT_APP_WH'),
            'role' => env('SNOWFLAKE_ROLE', 'ACCOUNTADMIN'),
            'username' => env('SNOWFLAKE_USERNAME', 'CONFLICTREPORT_USER'),
            'password' => env('SNOWFLAKE_PASSWORD'),
            'private_key' => env('SNOWFLAKE_PRIVATE_KEY_FILE', 'C:/Users/ArunGupta/.snowflake/cmsfkey.pem'),
            'auth_method' => env('SNOWFLAKE_AUTH_METHOD', 'keypair'),
            'charset' => 'utf8',
            'prefix' => '',
            'prefix_indexes' => true,
            'options' => [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_TIMEOUT => 30,
            ],
        ],

    ],

];
