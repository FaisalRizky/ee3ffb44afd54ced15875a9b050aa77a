<?php

namespace Providers;

use AltoRouter;
use Providers\OAuthProvider;

class RouterProvider
{
    private $router;
    private $oauthProvider;

    public function __construct(array $config, OAuthProvider $oauthProvider)
    {
        $this->oauthProvider = $oauthProvider;
        $this->router = new AltoRouter();
        $this->initializeRoutes();
    }

    private function initializeRoutes()
    {
        $this->loadRoutesFromFiles();
    }

    private function loadRoutesFromFiles()
    {
        $routesDir = dirname(__FILE__, 2) . '/routes';
        $files = glob($routesDir . '/*.php');
        
        foreach ($files as $file) {
            $routeGroup = require $file;

            if ($routeGroup instanceof RouteGroupProvider) {
                foreach ($routeGroup->getRoutes() as $route) {
                    $this->router->map(
                        $route['method'],
                        $route['path'],
                        [
                            'handler' => $route['handler'],
                            'middlewares' => $route['middlewares']
                        ]
                    );
                }
            }
        }
    }

    public function handleRequest()
    {
        $requestUri = $_SERVER['REQUEST_URI'];
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        
        // Match the current request
        $match = $this->router->match($requestUri, $requestMethod);

        if ($match) {
            $target = $match['target'];
            $handler = $target['handler'];
            $middlewares = $target['middlewares'];

            // Execute middlewares
            foreach ($middlewares as $middleware) {
                if (is_callable($middleware)) {
                    call_user_func($middleware);
                } else {
                    echo 'Middleware is not callable: ' . print_r($middleware, true);
                    exit();
                }
            }

            // Call the controller action
            $controllerAction = $handler;
            if (is_array($controllerAction) && count($controllerAction) === 2) {
                $controllerName = $controllerAction[0];
                $action = $controllerAction[1];

                if (class_exists($controllerName)) {
                    $controller = new $controllerName();
                    if (is_callable([$controller, $action])) {
                        call_user_func([$controller, $action]);
                    } else {
                        echo 'Action method is not callable: ' . $action;
                    }
                } else {
                    echo 'Controller class does not exist: ' . $controllerName;
                }
            } else {
                echo 'Invalid controller action format: ' . print_r($handler, true);
            }
        } else {
            echo '404 Not Found';
        }
    }
}
