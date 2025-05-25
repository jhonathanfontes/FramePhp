<?php

// Define base path
define('BASE_PATH', dirname(__DIR__));

// Load Composer's autoloader
require BASE_PATH . '/vendor/autoload.php';

// Load environment variables
$dotenv = \Dotenv\Dotenv::createImmutable(BASE_PATH);
$dotenv->load();

use Core\Config\Constants;
Constants::init();

// Load bootstrap
require_once BASE_PATH . '/bootstrap/app.php';

