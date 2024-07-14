<?php

namespace Controllers;

use Requests\EmailRequest;
use Providers\QueueProvider;
use Services\EmailService;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Providers\BaseControllerProvider;
use Illuminate\Http\Request; // Ensure this import

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
                null,
                400,
                'Validation failed',
                ['errors' => $emailRequest->getErrors()]
            );
        }

        try {
            $this->emailService->sendEmail($emailRequest->getData());
            // Using the send method from BaseControllerProvider
            $this->send(
                [
                    'email_id' =>  $this->emailService->getEmailId()
                ],
                200,
                'Email sent successfully',
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
