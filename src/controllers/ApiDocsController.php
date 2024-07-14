<?php

namespace Controllers;

use Providers\BaseControllerProvider;

class ApiDocsController extends BaseControllerProvider
{
    public function getApiDocs()
    {
        $this->view('api-docs');
    }

    public function swagger()
    {
        $filePath = dirname(__FILE__, 3) . '/public/swagger.json';
        header('Content-Type: application/json');

        if (file_exists($filePath)) {
            echo file_get_contents($filePath);
        } else {
            header('HTTP/1.0 404 Not Found');
            echo json_encode(['status' => 404, 'message' => 'File not found']);
        }

        exit();
    }
}
