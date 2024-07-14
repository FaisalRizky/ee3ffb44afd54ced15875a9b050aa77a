<?php

namespace Providers;

class BaseMiddlewareProvider
{
    protected function sendUnauthorizedResponse($message = 'Unauthorized')
    {
        header('HTTP/1.1 401 Unauthorized');
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 401,
            'message' => $message
        ]);
        exit();
    }
}
