<?php

namespace config;

use Dotenv\Dotenv;

/**
 * Class Config
 * Handles loading and accessing environment variables.
 */
class Config
{
    private $env;

    public function __construct()
    {
        // Load .env file
        $dotenv = Dotenv::createImmutable(dirname(__DIR__, 1));
        $dotenv->load();

        // Set default values if not provided in .env
        $this->env = [
            'APP_NAME' => $this->env('APP_NAME', 'default-app-name'),
            'APP_ENV' => $this->env('APP_ENV', 'local'),
            'APP_KEY' => $this->env('APP_KEY', 'default-key'),
            'APP_DEBUG' => $this->env('APP_DEBUG', true),
            'APP_URL' => $this->env('APP_URL', 'localhost:8000'),

            'DB_CONNECTION' => $this->env('DB_CONNECTION', 'pgsql'),
            'DB_HOST' => $this->env('DB_HOST', '127.0.0.1'),
            'DB_PORT' => $this->env('DB_PORT', 5432),
            'DB_DATABASE' => $this->env('DB_DATABASE', 'postgres'),
            'DB_USERNAME' => $this->env('DB_USERNAME', 'root'),
            'DB_PASSWORD' => $this->env('DB_PASSWORD', ''),

            'RABBITMQ_HOST' => $this->env('RABBITMQ_HOST', '127.0.0.1'),
            'RABBITMQ_PORT' => $this->env('RABBITMQ_PORT', 5672),
            'RABBITMQ_LOGIN' => $this->env('RABBITMQ_LOGIN', 'guest'),
            'RABBITMQ_PASSWORD' => $this->env('RABBITMQ_PASSWORD', 'guest'),

            'MAIL_MAILER' => $this->env('MAIL_MAILER', 'smtp'),
            'MAIL_HOST' => $this->env('MAIL_HOST', 'sandbox.smtp.mailtrap.io'),
            'MAIL_PORT' => $this->env('MAIL_PORT', 587),
            'MAIL_USERNAME' => $this->env('MAIL_USERNAME', 'default@example.com'),
            'MAIL_PASSWORD' => $this->env('MAIL_PASSWORD', 'defaultpassword'),
            'MAIL_ENCRYPTION' => $this->env('MAIL_ENCRYPTION', 'tls'),

            'PGADMIN_EMAIL' => $this->env('PGADMIN_EMAIL', 'root@mail.me'),
            'PGADMIN_PASSWORD' => $this->env('PGADMIN_PASSWORD', 'root'),

            // Add OAuth configuration variables
            'OAUTH_CLIENT_ID' => $this->env('OAUTH_CLIENT_ID', ''),
            'OAUTH_CLIENT_SECRET' => $this->env('OAUTH_CLIENT_SECRET', ''),
            'OAUTH_REDIRECT_URI' => $this->env('OAUTH_REDIRECT_URI', ''),
            'OAUTH_AUTHORIZE_URL' => $this->env('OAUTH_AUTHORIZE_URL', ''),
            'OAUTH_ACCESS_TOKEN_URL' => $this->env('OAUTH_ACCESS_TOKEN_URL', ''),
            'OAUTH_RESOURCE_OWNER_DETAILS_URL' => $this->env('OAUTH_RESOURCE_OWNER_DETAILS_URL', ''),
        ];
    }

    /**
     * Retrieve an environment variable with a default fallback.
     *
     * @param string $key The environment variable key.
     * @param mixed $default The default value if the environment variable is not set.
     * @return mixed The environment variable value or the default value.
     */
    private function env($key, $default = null)
    {
        return getenv($key) ?: $default;
    }

    /**
     * Get the configuration values.
     *
     * @return array
     */
    public function getConfig()
    {
        return $this->env;
    }
}
