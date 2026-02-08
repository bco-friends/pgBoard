#!/usr/bin/env php
<?php
$baseDir = dirname(__DIR__);

if (!is_readable($baseDir . '/vendor/autoload.php')) {
  echo 'You must run `composer install` to use this command.';
  exit(1);
}

require_once "{$baseDir}/vendor/autoload.php";
require_once "{$baseDir}/config/config.php";

$app = \PgBoard\PgBoard\CommandService::load();
