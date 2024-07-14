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

$input = new StringInput('migrate');
$output = new ConsoleOutput();
$manager = new Manager($phinxConfig, $input, $output);

$manager->migrate($phinxConfig->getDefaultEnvironment());

echo "Migrations completed.\n";
