<?php

require_once __DIR__ . '/vendor/autoload.php';

$dotenv = new \Symfony\Component\Dotenv\Dotenv();

// Use .env if available, otherwise fall back to .example.env
if (file_exists(__DIR__ . '/.env')) {
    $dotenv->load(__DIR__ . '/.env');
} elseif (file_exists(__DIR__ . '/.example.env')) {
    $dotenv->load(__DIR__ . '/.example.env');
}

require_once __DIR__ . '/constants.php';

// Always use the default language file so analysis doesn't depend on
// user-generated config files
require_once __DIR__ . '/lang/en.default.php';