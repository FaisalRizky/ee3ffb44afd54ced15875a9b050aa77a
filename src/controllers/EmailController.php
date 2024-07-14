<?php

namespace Controllers;

use Requests\EmailRequest;
use Providers\QueueProvider;
use Services\EmailService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Providers\BaseControllerProvider;

class EmailController extends BaseControllerProvider
{
    private $emailService;
    private $logger;

    public function __construct()
    {
        $queueProvider = QueueProvider::getInstance();
        $this->emailService = new EmailService($queueProvider);
        $this->logger = $this->initializeLogger();
    }

    public function sendMail(Request $request)
    {
        $emailRequest = new EmailRequest($request->all());

        if (!$emailRequest->validate()) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $emailRequest->getErrors()
            ], 400);
        }

        try {
            $this->emailService->sendEmail($emailRequest->getData());
            echo json_encode(['status' => 'success', 'message' => 'Email sent successfully'], 200);
        } catch (\Exception $e) {
            $this->logger->error('Failed to send email', ['exception' => $e->getMessage()]);
            echo json_encode(['status' => 'error', 'message' => 'Failed to send email'], 500);
        }
    }

    private function initializeLogger()
    {
        $logPath = dirname(__FILE__, 3) . '/logs/app.log';

        if (!is_dir(dirname($logPath))) {
            mkdir(dirname($logPath), 0777, true);
        }

        $logger = new Logger('email_controller');
        $logger->pushHandler(new StreamHandler($logPath, Logger::DEBUG));

        return $logger;
    }
}
