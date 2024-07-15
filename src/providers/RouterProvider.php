<?php

namespace Providers;

use AltoRouter;
use Illuminate\Http\Request;
use Providers\OAuthProvider; // Ensure this is included

class RouterProvider extends BaseControllerProvider
{
    private $router;
    private $oauthProvider;

    public function __construct(array $config)
    {
        $this->router = new AltoRouter();
        $this->initializeRoutes();
        $this->oauthProvider = new OAuthProvider($config); // Initialize OAuthProvider
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
            $routeGroup = include $file;

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

            if ($controllerName === 'OAuthController') {
                $this->handleOAuth($action);
            } else {
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

        // Handle other classes
        if (class_exists($className)) {
            return new $className();
        }

        throw new \Exception('Class ' . $className . ' not found');
    }

    private function createRequest()
    {
        // Create and return a Request object
        return Request::createFromGlobals();
    }

    private function handleOAuth($action)
    {
        switch ($action) {
            case 'authorize':
                $this->handleOAuthAuthorization();
                break;
            
            case 'token':
                $this->handleOAuthToken();
                break;

            case 'userinfo':
                $this->handleUserInfo();
                break;

            default:
                $this->send(null, 404, 'OAuth action not found');
                break;
        }
    }

    private function handleOAuthAuthorization()
    {
        // Redirect to OAuth provider's authorization URL
        header('Location: ' . $this->oauthProvider->getAuthorizationUrl());
        exit();
    }

    private function handleOAuthToken()
    {
        // Handle OAuth token exchange
        if (isset($_GET['code'])) {
            $authorizationCode = $_GET['code'];
            try {
                $accessToken = $this->oauthProvider->getAccessToken($authorizationCode);
                $_SESSION['access_token'] = $accessToken->getToken();
                header('Location: /userinfo');
            } catch (\Exception $e) {
                $this->send(null, 500, 'Error: ' . $e->getMessage());
            }
        } else {
            $this->send(null, 400, 'Authorization code is missing');
        }
    }

    private function handleUserInfo()
    {
        // Handle user info request
        if (isset($_SESSION['access_token'])) {
            $accessToken = $_SESSION['access_token'];
            try {
                $user = $this->oauthProvider->getResourceOwner($accessToken);
                echo '<pre>';
                print_r($user);
                echo '</pre>';
            } catch (\Exception $e) {
                $this->send(null, 500, 'Error: ' . $e->getMessage());
            }
        } else {
            $this->send(null, 401, 'Access token is missing');
        }
    }
}
