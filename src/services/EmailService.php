<?php

namespace Services;

use Models\Email;
use Providers\QueueProvider;
use PhpAmqpLib\Message\AMQPMessage;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Ramsey\Uuid\Uuid;

class EmailService
{
    private $queueProvider;
    private $logger;
    private $emailId; // Property to store the UUID

    public function __construct(QueueProvider $queueProvider)
    {
        $this->queueProvider = $queueProvider;
        $this->logger = $this->initializeLogger();
    }

    public function sendEmail(array $emailData): array
    {
        // Generate unique email_id
        $this->emailId = Uuid::uuid4()->toString();
        $emailData['emailId'] = $this->emailId;

        // Save email to database
        $this->saveEmailToDatabase($emailData);

        // Publish email to RabbitMQ
        $connection = $this->queueProvider->getConnection();
        $channel = $connection->channel();
        $channel->queue_declare('email_queue', false, false, false, false);

        try {
            $message = new AMQPMessage(
                json_encode($emailData), [
                'content_type' => 'application/json',
                'delivery_mode' => 2
                ]
            );
            $channel->basic_publish($message, '', 'email_queue');
            $this->logger->info('Email sent to RabbitMQ', $emailData);

            // Return the UUID as part of the response
            return [
                'status' => 'success',
                'message' => 'Email sending on process',
                'emailId' => $this->emailId
            ];
        } catch (\Exception $e) {
            $this->logger->error('Failed to send email to RabbitMQ: ' . $e->getMessage(), $emailData);
            throw $e; // Re-throw the exception for further handling if necessary
        } finally {
            $channel->close();
            $connection->close();
        }
    }

    public function getEmailStatus(string $emailId): array
    {
        try {
            // Query email status using email_id
            $email = Email::where('emailId', $emailId)->first();
            
            if ($email) {
                return [
                    'email_id' => $email->emailId,
                    'status' => $email->status,
                    'remarks' => $email->remarks
                ];
            } else {
                return [
                    'email_id' => $emailId,
                    'status' => 'not found',
                    'remarks' => 'email not found'
                ];
            }
        } catch (\Exception $e) {
            $this->logger->error('Failed to retrieve email status: ' . $e->getMessage(), ['email_id' => $emailId]);
            return [
                'status' => 'error',
                'message' => 'email not found'
            ];
        }
    }

    private function saveEmailToDatabase(array $emailData)
    {
        try {
            // Create a new Email record
            Email::create(
                [
                'module' => $emailData['module'],
                'emailId' => $emailData['emailId'],
                'sender' => $emailData['sender'],
                'recipient' => $emailData['recipient'],
                'subject' => $emailData['subject'],
                'content' => $emailData['content'],
                'status' => $emailData['status'] ?? 'pending', // Add status if provided
                'remarks' => $emailData['remarks'] ?? '' // Add remarks if provided
                ]
            );
            $this->logger->info('Email saved to database', $emailData);
        } catch (\Exception $e) {
            $this->logger->error('Failed to save email to database: ' . $e->getMessage(), $emailData);
            throw $e; // Re-throw the exception for further handling if necessary
        }
    }

    private function initializeLogger()
    {
        $logPath = dirname(__FILE__, 3) . '/logs/app.log';

        if (!is_dir(dirname($logPath))) {
            mkdir(dirname($logPath), 0777, true);
        }

        $logger = new Logger('email_service');
        $logger->pushHandler(new StreamHandler($logPath, Logger::DEBUG));

        return $logger;
    }

    // Getter for emailId
    public function getEmailId(): ?string
    {
        return $this->emailId;
    }
}
