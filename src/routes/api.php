<?php

use Config\Config;
use Providers\RouteGroupProvider;
use Controllers\UserController;
use Controllers\EmailController;
use Middleware\OAuthMiddleware;
use Controllers\ApiDocsController;
use Providers\OAuthProvider;

$config = new Config();
$appConfig = $config->getConfig();

$oauthProvider = new OAuthProvider($appConfig);
$oauthMiddleware = new OAuthMiddleware($oauthProvider);

$routeGroup = new RouteGroupProvider();

// Define API docs route without middleware
$routeGroup->addRoute('GET', '/', [ApiDocsController::class, 'getApiDocs']);

// Define email routes with middleware
$routeGroup->addRoute('POST', '/emails/send', [EmailController::class, 'sendMail'], []);

// Define asset route
$routeGroup->addRoute('GET', '/swagger', [ApiDocsController::class, 'swagger']);

return $routeGroup;
