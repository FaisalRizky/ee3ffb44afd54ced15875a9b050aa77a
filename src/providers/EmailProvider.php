<?php

namespace Providers;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Config\Config; // Import the Config class

class EmailProvider
{
    private static $logger;
    private static $config;

    /**
     * Initialize the EmailProvider and set up logging and configuration.
     *
     * @param array $config
     */
    public static function initialize(array $config)
    {
        self::initializeLogger();
        self::$config = $config; // Set the static config property
    }

    /**
     * Send an email.
     *
     * @param array $emailData
     * @throws \Exception
     */
    public static function sendEmail(array $emailData)
    {
        self::initializeLogger(); // Ensure logger is initialized

        $mail = new PHPMailer(true);

        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host = self::$config['MAIL_HOST'];
            $mail->SMTPAuth = true;
            $mail->Username = self::$config['MAIL_USERNAME'];
            $mail->Password = self::$config['MAIL_PASSWORD'];
            $mail->SMTPSecure = self::$config['MAIL_ENCRYPTION'];
            $mail->Port = self::$config['MAIL_PORT'];

            // Recipients
            $mail->setFrom($emailData['sender']);
            $mail->addAddress($emailData['recipient']);

            // Content
            $mail->isHTML(true);
            $mail->Subject = $emailData['subject'];
            $mail->Body = $emailData['content'];

            // Send email
            $mail->send();
            self::$logger->info('Email sent successfully', $emailData);
        } catch (Exception $e) {
            self::$logger->error('Failed to send email: ' . $mail->ErrorInfo, $emailData);
            throw $e;
        }
    }

    /**
     * Initialize logger for the EmailProvider.
     */
    private static function initializeLogger()
    {
        if (self::$logger === null) {
            // Create a log channel
            $logPath = dirname(__FILE__, 3) . '/logs/email.log';

            // Ensure the logs directory exists
            if (!is_dir(dirname($logPath))) {
                mkdir(dirname($logPath), 0777, true);
            }

            // Create a new Logger instance
            self::$logger = new Logger('email_provider');
            
            // Add a StreamHandler to the logger
            self::$logger->pushHandler(new StreamHandler($logPath, Logger::DEBUG));
        }
    }
}
