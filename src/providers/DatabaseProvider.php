<?php

namespace Providers;

use Illuminate\Database\Capsule\Manager as Capsule;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class DatabaseProvider
{
    private static $logger;

    public static function initialize(array $config)
    {
        // Initialize logger
        self::initializeLogger();

        try {
            // Initialize Capsule (Illuminate Database)
            $capsule = new Capsule;

            // Add database connection
            $capsule->addConnection([
                'driver'    => $config['DB_CONNECTION'],
                'host'      => $config['DB_HOST'],
                'database'  => $config['DB_DATABASE'],
                'username'  => $config['DB_USERNAME'],
                'password'  => $config['DB_PASSWORD'],
                'charset'   => 'utf8',
                'prefix'    => '',
                'schema'    => 'public',
            ]);

            // Set the Capsule instance as globally accessible
            $capsule->setAsGlobal();
            $capsule->bootEloquent();

            // Log success message
            self::$logger->info("Database connection established successfully.");

        } catch (\Exception $e) {
            // Log error message
            self::$logger->error("Failed to connect to the database: " . $e->getMessage());
        }
    }

    private static function initializeLogger()
    {
        if (self::$logger === null) {
            // Create a log channel
            $logPath = dirname(__FILE__, 3) . '/logs/app.log';

            // Ensure the logs directory exists
            if (!is_dir(dirname($logPath))) {
                mkdir(dirname($logPath), 0777, true);
            }

            // Create a new Logger instance
            self::$logger = new Logger('database');
            
            // Add a StreamHandler to the logger
            self::$logger->pushHandler(new StreamHandler($logPath, Logger::DEBUG));
        }
    }
}
