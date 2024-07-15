<?php

use Config\Config;
use Providers\RouteGroupProvider;
use Providers\OAuthProvider;
use Controllers\UserController;
use Controllers\EmailController;
use Middleware\OAuthMiddleware;
use Controllers\ApiDocsController;
use Controllers\OAuthController;

// Load configuration
$config = new Config();
$appConfig = $config->getConfig();

// Initialize OAuth provider and middleware
$oauthProvider = new OAuthProvider($appConfig);
$oauthMiddleware = new OAuthMiddleware($oauthProvider);

// Initialize route group provider
$routeGroup = new RouteGroupProvider();

// Define API docs route without middleware
$routeGroup->addRoute('GET', '/', [ApiDocsController::class, 'getApiDocs'], []);

// Define email routes with OAuth middleware
$routeGroup->addRoute('POST', '/emails/send', [EmailController::class, 'sendMail'], [$oauthMiddleware]);

// Define asset route without middleware
$routeGroup->addRoute('GET', '/swagger', [ApiDocsController::class, 'swagger'], [$oauthMiddleware]);

// Define OAuth callback route with OAuthCallbackController
$routeGroup->addRoute('GET', '/api/auth/callback/google', [OAuthController::class, 'handleCallback']);

// Define OAuth routes with OAuthController
$routeGroup->addRoute('GET', '/oauth/authorize', [OAuthController::class, 'authorize']);
$routeGroup->addRoute('POST', '/oauth/token', [OAuthController::class, 'token']);
$routeGroup->addRoute('GET', '/oauth/userinfo', [OAuthController::class, 'resourceOwnerDetails']);

// Return route group
return $routeGroup;
