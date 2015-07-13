<?php
namespace yapaf;
require_once 'Response.php';
require_once 'Request.php';
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
        $this->request = new Request;
        $this->response = new Response;
    }

    public function run() {
        $requestURI = $this->request->getRequestUri();
        $name = $this->request->get('name');
        $this->response->set('test ok: ' . $requestURI . ' - says name is: ' .$name);
        return $this->response->render();
    }
}


