<?php

namespace Providers;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Exception\AMQPException;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Config\Config; // Import the Config class

class QueueProvider
{
    private static $instance = null;
    private static $logger;
    private static $connection;

    // Private constructor to prevent instantiation
    private function __construct()
    {
    }

    // Private clone method to prevent cloning
    private function __clone()
    {
    }

    // Private wakeup method to prevent deserialization
    public function __wakeup()
    {
    }

    // Method to get the singleton instance
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
            self::initialize();
        }
        return self::$instance;
    }

    public static function initialize()
    {
        // Get configuration from Config class
        $config = (new Config())->getConfig();

        // Initialize logger
        self::initializeLogger();

        try {
            // Establish RabbitMQ connection
            self::$connection = new AMQPStreamConnection(
                $config['RABBITMQ_HOST'],
                $config['RABBITMQ_PORT'],
                $config['RABBITMQ_LOGIN'],
                $config['RABBITMQ_PASSWORD']
            );

            // Log success message
            self::$logger->info("Connected to RabbitMQ successfully.");

        } catch (AMQPException $e) {
            // Log error message
            self::$logger->error("Failed to connect to RabbitMQ: " . $e->getMessage());
        }
    }

    public static function getConnection()
    {
        return self::$connection;
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
            self::$logger = new Logger('queue');
            
            // Add a StreamHandler to the logger
            self::$logger->pushHandler(new StreamHandler($logPath, Logger::DEBUG));
        }
    }
}
