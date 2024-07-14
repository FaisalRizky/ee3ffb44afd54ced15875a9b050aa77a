<?php

namespace Providers;

use AltoRouter;
use Middleware\OAuthMiddleware;

class RouterProvider
{
    private $router;

    public function __construct(array $config)
    {
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
            $controllerAction = $target['handler'];
            $middlewares = $target['middlewares'];

            // Execute middlewares
            foreach ($middlewares as $middleware) {
                if ($middleware instanceof OAuthMiddleware) {
                    $middleware->handle();
                } else {
                    echo 'Middleware is not an instance of OAuthMiddleware';
                    exit();
                }
            }

            // Call the controller action
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
            echo '404 Not Found';
        }
    }
}
