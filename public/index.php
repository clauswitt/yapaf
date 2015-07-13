<?php
if (php_sapi_name() == "cli-server") {

    $endsWith = function ($haystack, $needle) {
        // search forward starting from end minus needle length characters
        return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== FALSE);
    };

    // running under built-in server so
    $path = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
    $ext = pathinfo($path, PATHINFO_EXTENSION);

    // If the file exists then return false and let the server handle it
    if (file_exists($_SERVER["DOCUMENT_ROOT"] . $path) && $path != '/') {
        return false;
    }
}


$rootPath = realpath(__DIR__ . '/..');
$applicationPath = realpath(__DIR__ . '/../app');

require_once $rootPath . '/Application.php';
$application = new \yapaf\Application($rootPath, $applicationPath, dirname(__FILE__));

echo $application->run();
