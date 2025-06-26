<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Connection;
use PDO;

class SnowflakeServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        Connection::resolverFor('odbc', function ($connection, $database, $prefix, $config) {
            $config['name'] = 'snowflake';
            $config['pdo'] = $this->createPdoConnection($config);
            return new Connection($config['pdo'], $database, $prefix, $config);
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Create a PDO connection for the Snowflake connection.
     */
    protected function createPdoConnection(array $config)
    {
        try {
            // Build DSN string
            $dsn = "odbc:{$config['dsn']}";
            $options = $config['options'] ?? [];

            // Create PDO connection
            $pdo = new PDO($dsn, $config['username'], $config['password'], $options);

            // Set error mode
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            return $pdo;
        } catch (\PDOException $e) {
            throw new \Exception('Failed to create PDO connection: ' . $e->getMessage());
        }
    }
} 