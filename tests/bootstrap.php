<?php
// Prevent config.php from initializing the full application
$commandline = true;

$baseDir = dirname(__FILE__, 2);

// Load dependencies and constants
require_once $baseDir . '/vendor/autoload.php';

$dotenv = new \Symfony\Component\Dotenv\Dotenv();
$dotenv->load($baseDir . '/.env');

require_once $baseDir . '/constants.php';
require_once $baseDir . '/core.php';
require_once $baseDir . '/error.php';

// Load language file
require_once $baseDir . '/lang/' . LANG . '.php';

// Load class files (but don't initialize global objects)
$classFiles = [
    'class/DB.php',
    'class/Query.php',
    'class/Security.php',
    'class/Core.php',
    'class/Style.php',
    'class/Base.php',
    'class/List.php',
    'class/View.php',
    'class/Parse.php',
    'class/Form.php',
    'class/Data.php',
    'class/Search.php',
    'class/Admin.php',
];

foreach ($classFiles as $file) {
    if (is_readable($baseDir . '/config/' . $file)) {
        require_once $baseDir . '/config/' . $file;
    } elseif (is_readable($baseDir . '/' . $file)) {
        require_once $baseDir . '/' . $file;
    }
}

// Load Plugin.php
if (is_readable($baseDir . '/config/class/Plugin.php')) {
    require_once $baseDir . '/config/class/Plugin.php';
} else {
    require_once $baseDir . '/class/Plugin.php';
}
