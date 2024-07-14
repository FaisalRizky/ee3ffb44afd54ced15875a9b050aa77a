<?php

use Config\Config;
use Providers\DatabaseProvider;
use Services\EmailConsumerService;

// Autoload Composer dependencies
require __DIR__ . '/vendor/autoload.php';

try {
    // Load application configuration
    $config = new Config();
    $appConfig = $config->getConfig();

    // Check if configuration is valid
    if (empty($appConfig) || !isset($appConfig['RABBITMQ_HOST']) || !isset($appConfig['DB_CONNECTION'])) {
        throw new Exception('Invalid configuration: Missing RabbitMQ host or database connection details.');
    }

    // Initialize DatabaseProvider with configuration
    DatabaseProvider::initialize($appConfig);

    // Initialize EmailConsumerService with RabbitMQ configuration
    $emailConsumerService = new EmailConsumerService($appConfig);

    // Run the message consumer
    $emailConsumerService->consumeMessages();
} catch (Exception $e) {
    // Handle exceptions and errors
    echo "An error occurred: " . $e->getMessage() . "\n";
    // Optionally log the error or perform other actions
}
