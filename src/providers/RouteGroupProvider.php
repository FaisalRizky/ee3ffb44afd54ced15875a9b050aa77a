<?php

namespace Providers;

class RouteGroupProvider
{
    private $routes = [];

    public function addRoute(string $method, string $path, array $handler, array $middlewares = [])
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
