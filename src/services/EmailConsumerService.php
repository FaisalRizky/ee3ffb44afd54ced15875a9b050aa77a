<?php

namespace Services;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Providers\EmailProvider;
use Models\Email;

class EmailConsumerService
{
    private $connection;
    private $channel;

    public function __construct(array $config)
    {
        $this->connection = new AMQPStreamConnection(
            $config['RABBITMQ_HOST'],
            $config['RABBITMQ_PORT'],
            $config['RABBITMQ_LOGIN'],
            $config['RABBITMQ_PASSWORD']
        );

        $this->channel = $this->connection->channel();

        // Initialize EmailProvider with configuration
        EmailProvider::initialize($config);
    }

    public function consumeMessages()
    {
        $this->channel->queue_declare('email_queue', false, false, false, false);

        echo "Waiting for messages. To exit press CTRL+C\n";

        $callback = function (AMQPMessage $msg) {
            $data = json_decode($msg->body, true);

            try {
                // Send email
                EmailProvider::sendEmail($data);

                // Update database to mark as sent
                $this->updateDatabaseStatus($data['emailId'], 'success');

                // Acknowledge the message
                $msg->ack();

                echo "Processed message: " . $msg->body . "\n";
            } catch (\Exception $e) {
                // Log error message
                echo "Failed to process message: " . $e->getMessage() . "\n";

                // Log the error message for further investigation
                $this->logError($data, $e->getMessage());

                // Update database to mark as failed
                $this->updateDatabaseStatus($data['emailId'], 'failed');

                // Acknowledge the message and do not requeue it
                $msg->ack(); // Use $msg->nack(false, false); if you want to reject without requeuing
            }
        };

        $this->channel->basic_consume('email_queue', '', false, false, false, false, $callback);

        while ($this->channel->is_consuming()) {
            $this->channel->wait();
        }
    }


    private function updateDatabaseStatus($emailId, $status)
    {
        try {
            // Find the email record and update its status
            $email = Email::where('emailId', $emailId)->first();

            if ($email) {
                $email->status = $status;
                $email->sent_at = (new \DateTime())->format('Y-m-d H:i:s'); // Get current timestamp
                $email->save();

                echo "Database updated for emailId: $emailId with status: $status\n";
            } else {
                echo "Email record not found for emailId: $emailId\n";
            }
        } catch (\Exception $e) {
            echo "Failed to update database: " . $e->getMessage() . "\n";
        }
    }

    private function logError($data, $errorMessage)
    {
        // Log error details to a file or monitoring system
        $logPath = dirname(__FILE__, 3) . '/logs/email_errors.log';
        
        if (!is_dir(dirname($logPath))) {
            mkdir(dirname($logPath), 0777, true);
        }

        $errorLog = sprintf(
            "[%s] Error processing message with emailId %s: %s\n",
            date('Y-m-d H:i:s'),
            $data['emailId'],
            $errorMessage
        );

        file_put_contents($logPath, $errorLog, FILE_APPEND);
    }
}
