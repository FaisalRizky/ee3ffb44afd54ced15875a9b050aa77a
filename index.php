<?php

use Config\Config;
use Providers\DatabaseProvider;
use Providers\QueueProvider;
use Providers\RouterProvider;

// Autoload Composer dependencies
require __DIR__ . '/vendor/autoload.php';

// Load application configuration
$config = new Config();
$appConfig = $config->getConfig();

// Initialize DatabaseProvider with configuration
DatabaseProvider::initialize($appConfig);

// Initialize QueueProvider with configuration
QueueProvider::initialize($appConfig);

// Initialize RouterProvider with configuration and OAuthProvider
$routerProvider = new RouterProvider($appConfig);

// Handle incoming request
$routerProvider->handleRequest();
