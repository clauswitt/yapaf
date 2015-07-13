<?php
namespace yapaf;
require_once 'RequestHandler.php';
class Application
{
    protected $rootPath;
    protected $applicationPath;
    protected $publicPath;
    protected $request;
    protected $response;
    public function __construct($rootPath, $applicationPath, $publicPath) {
        $this->rootPath = $rootPath;
        $this->applicationPath = $applicationPath;
        $this->publicPath = $publicPath;
        require_once $this->applicationPath . '/routes.php';
    }

    public function run() {
        $requestHandler = new RequestHandler($this->applicationPath);
        return $requestHandler->handle();
    }
}


