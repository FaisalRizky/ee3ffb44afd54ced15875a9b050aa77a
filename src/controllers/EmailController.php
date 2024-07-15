<?php

namespace Controllers;

use Requests\EmailRequest;
use Requests\EmailStatusRequest;
use Providers\QueueProvider;
use Services\EmailService;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Providers\BaseControllerProvider;
use Illuminate\Http\Request;

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
            // Using the send method from BaseControllerProvider
            $this->send(
                ['errors' => $emailRequest->getErrors()],
                400,
                'Validation failed'
            );
            return;
        }

        try {
            $this->emailService->sendEmail($emailRequest->getData());
            // Using the send method from BaseControllerProvider
            $this->send(
                [
                    'email_id' => $this->emailService->getEmailId()
                ],
                200,
                'Email sending in process'
            );
        } catch (\Exception $e) {
            $this->logger->error('Failed to send email', ['exception' => $e->getMessage()]);
            // Using the send method from BaseControllerProvider
            $this->send(
                null,
                500,
                'Failed to send email'
            );
        }
    }

    public function checkStatus(Request $request)
    {
        $emailStatusRequest = new EmailStatusRequest($request->all());

        if (!$emailStatusRequest->validate()) {
            // Using the send method from BaseControllerProvider
            $this->send(
                ['errors' => $emailStatusRequest->getErrors()],
                400,
                'Validation failed'
            );
            return;
        }

        try {
            $statusData = $this->emailService->getEmailStatus($emailStatusRequest->getData()['email_id']);
            // Using the send method from BaseControllerProvider
            $this->send(
                $statusData,
                200,
                'Email status retrieved successfully'
            );
        } catch (\Exception $e) {
            $this->logger->error('Failed to retrieve email status', ['exception' => $e->getMessage()]);
            // Using the send method from BaseControllerProvider
            $this->send(
                null,
                500,
                'Failed to retrieve email status'
            );
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
