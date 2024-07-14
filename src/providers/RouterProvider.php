<?php

namespace Providers;

use AltoRouter;
use Illuminate\Http\Request; // Ensure this import

class RouterProvider extends BaseControllerProvider
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
            } else {
                $this->send(null, 500, 'Invalid route group provider');
            }
        }
    }

    public function handleRequest()
    {
        $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $requestMethod = $_SERVER['REQUEST_METHOD'];

        // Match the current request
        $match = $this->router->match($requestUri, $requestMethod);

        if ($match) {
            $target = $match['target'];
            $controllerAction = $target['handler'];
            $middlewares = $target['middlewares'];

            // Execute middlewares
            foreach ($middlewares as $middleware) {
                if (is_object($middleware) && method_exists($middleware, 'handle')) {
                    $middleware->handle();
                } else {
                    $this->send(null, 500, 'Middleware is not callable or does not have a handle method');
                }
            }

            // Call the controller action
            $controllerName = $controllerAction[0];
            $action = $controllerAction[1];

            if (class_exists($controllerName)) {
                $controller = new $controllerName();
                if (is_callable([$controller, $action])) {
                    // Resolve dependencies and call the action
                    $reflection = new \ReflectionMethod($controller, $action);
                    $params = [];

                    foreach ($reflection->getParameters() as $param) {
                        $type = $param->getType();
                        if ($type && !$type->isBuiltin()) {
                            $className = $type->getName();
                            $params[] = $this->resolveClass($className);
                        } else {
                            $params[] = null; // Handle non-class parameters if necessary
                        }
                    }

                    call_user_func_array([$controller, $action], $params);
                } else {
                    $this->send(null, 500, 'Action method is not callable: ' . $action);
                }
            } else {
                $this->send(null, 500, 'Controller class does not exist: ' . $controllerName);
            }
        } else {
            $this->send(null, 404, '404 Not Found');
        }
    }

    private function resolveClass($className)
    {
        // Simple dependency resolution (can be extended for more complex scenarios)
        if ($className === 'Illuminate\Http\Request') {
            return $this->createRequest();
        }
        return new $className();
    }

    private function createRequest()
    {
        // Create and return a Request object
        return Request::createFromGlobals();
    }
}
