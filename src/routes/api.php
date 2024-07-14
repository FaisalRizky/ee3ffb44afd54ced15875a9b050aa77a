<?php

use Providers\RouteGroupProvider;
use Controllers\UserController;
// use Middleware\OAuthMiddleware;

// Initialize OAuth middleware
// $oauthMiddleware = new OAuthMiddleware($oauthProvider);

$routeGroup = new RouteGroupProvider();

// Define routes
$routeGroup->addRoute('GET', '/', [UserController::class, 'listUsers'], []);
$routeGroup->addRoute('POST', '/users', [UserController::class, 'createUser'], []);

return $routeGroup;
