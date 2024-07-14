<?php

namespace Services;

use Models\Email;
use Providers\QueueProvider;
use PhpAmqpLib\Message\AMQPMessage;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class EmailService
{
    private $queueProvider;
    private $logger;

    public function __construct(QueueProvider $queueProvider)
    {
        $this->queueProvider = $queueProvider;
        $this->logger = $this->initializeLogger();
    }

    public function sendEmail(array $emailData)
    {
        $connection = $this->queueProvider->getConnection();
        $channel = $connection->channel();
        $channel->queue_declare('email_queue', false, false, false, false);

        try {
            $message = new AMQPMessage(json_encode($emailData), [
                'content_type' => 'application/json',
                'delivery_mode' => 2
            ]);
            $channel->basic_publish($message, '', 'email_queue');
            $this->logger->info('Email sent to RabbitMQ', $emailData);
        } catch (\Exception $e) {
            $this->logger->error('Failed to send email to RabbitMQ: ' . $e->getMessage(), $emailData);
            throw $e; // Re-throw the exception for further handling if necessary
        } finally {
            $channel->close();
            $connection->close();
        }
    }

    private function initializeLogger()
    {
        $logPath = dirname(__FILE__, 2) . '/logs/app.log';

        if (!is_dir(dirname($logPath))) {
            mkdir(dirname($logPath), 0777, true);
        }

        $logger = new Logger('email_service');
        $logger->pushHandler(new StreamHandler($logPath, Logger::DEBUG));

        return $logger;
    }
}
