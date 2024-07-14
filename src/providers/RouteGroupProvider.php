<?php

namespace Providers;

class RouteGroupProvider
{
    private $routes = [];

    public function addRoute($method, $path, $handler, $middlewares = [])
    {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'handler' => $handler,
            'middlewares' => $middlewares
        ];
    }

    public function getRoutes()
    {
        return $this->routes;
    }
}
