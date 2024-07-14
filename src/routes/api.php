<?php

use Config\Config;
use Providers\RouteGroupProvider;
use Controllers\UserController;
use Middleware\OAuthMiddleware;
use Providers\OAuthProvider;

$config = new Config();
$appConfig = $config->getConfig();

// For instance, you might need to pass it as a dependency to this file
$oauthProvider = new OAuthProvider($appConfig);

// Initialize OAuth middleware with the OAuth provider
$oauthMiddleware = new OAuthMiddleware($oauthProvider);

$routeGroup = new RouteGroupProvider();

// Define routes with middleware
$routeGroup->addRoute('GET', '/', [UserController::class, 'listUsers'], [$oauthMiddleware]);
$routeGroup->addRoute('POST', '/users', [UserController::class, 'createUser'], [$oauthMiddleware]);

return $routeGroup;
