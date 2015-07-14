<?php
$rootPath = realpath(__DIR__ . '/..');
require_once $rootPath . '/../vendor/autoload.php';

if($_ENV['YAPAF_DEV_SERVER']==1) {
    \yapaf\DevServer::handle();
}

$applicationPath = $rootPath . '/app';
$application = new \yapaf\Application($rootPath, $applicationPath, dirname(__FILE__));
echo $application->run();
