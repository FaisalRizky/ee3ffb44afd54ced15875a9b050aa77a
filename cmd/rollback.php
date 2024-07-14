<?php

require 'vendor/autoload.php';

use Phinx\Config\Config as PhinxConfig;
use Phinx\Migration\Manager;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Providers\PhinxProvider; // Ensure the namespace is correct

// Get Phinx configuration from PhinxProvider
$phinxConfigArray = PhinxProvider::getConfig();
$phinxConfig = new PhinxConfig($phinxConfigArray);

// Create migration manager
$input = new StringInput('rollback');
$output = new ConsoleOutput();
$manager = new Manager($phinxConfig, $input, $output);

// Rollback migrations
$manager->rollback($phinxConfig->getDefaultEnvironment());

echo "Rollback completed.\n";
