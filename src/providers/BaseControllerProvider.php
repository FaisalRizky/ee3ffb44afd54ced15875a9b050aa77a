<?php

namespace Providers;

class BaseControllerProvider
{
    protected function send($data = null, $statusCode = 200, $message = '')
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode(
            [
            'status' => $statusCode,
            'message' => $message,
            'data' => $data ?? []
            ]
        );
        exit();
    }
    
    protected function view($viewName)
    {
        $filePath = dirname(__FILE__, 2) . '/views/' . $viewName . '.html';

        if (file_exists($filePath)) {
            header('Content-Type: text/html');
            readfile($filePath);
            exit();
        } else {
            $this->send('View not found', 404, 'Not Found');
        }
    }

    protected function asset($fileName)
    {
        $filePath = dirname(__FILE__, 3) . '/public/' . $fileName;
        if (file_exists($filePath)) {
            header('Content-Type: ' . mime_content_type($filePath));
            readfile($filePath);
            exit();
        } else {
            $this->send(null, 404, 'File not found');
        }
    }

}
