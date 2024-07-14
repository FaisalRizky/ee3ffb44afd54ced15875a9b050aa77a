<?php

namespace Providers;

use Config\Config as AppConfig;

/**
 * Class PhinxProvider
 * Provides Phinx configuration based on environment variables.
 */
class PhinxProvider
{
    /**
     * Get the Phinx configuration.
     *
     * @return array
     */
    public static function getConfig()
    {
        $config = new AppConfig();
        $env = $config->getConfig();

        // Determine the migration folder path
        $migrationPath = dirname(__FILE__, 2).'/database/migrations';
        
        // Output the migration folder path
        echo "Phinx migration folder path: " . $migrationPath . PHP_EOL;

        return [
            'paths' => [
                'migrations' => $migrationPath,
            ],
            'environments' => [
                'default_migration_table' => 'phinxlog',
                'default_environment' => 'development',
                'development' => [
                    'adapter' => 'pgsql',
                    'host' => $env['DB_HOST'],
                    'name' => $env['DB_DATABASE'],
                    'user' => $env['DB_USERNAME'],
                    'pass' => $env['DB_PASSWORD'],
                    'port' => $env['DB_PORT'],
                    'charset' => 'utf8',
                ],
            ],
        ];
    }
}
