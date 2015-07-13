<?php
namespace yapaf;
require_once 'Response.php';
class Application
{
    protected $rootPath;
    protected $applicationPath;
    protected $publicPath;
    protected $response;
    public function __construct($rootPath, $applicationPath, $publicPath) {
        $this->rootPath = $rootPath;
        $this->applicationPath = $applicationPath;
        $this->publicPath = $publicPath;
        $this->response = new Response;
    }

    public function run() {
        $this->response->set('test ok');
        return $this->response->render();
    }
}


