<?php
namespace yapaf;
class Application
{
    protected $rootPath;
    protected $applicationPath;
    protected $publicPath;
    protected $request;
    protected $response;
    public function __construct($applicationPath=null) {
        if(is_null($applicationPath)) {
            $applicationPath = realpath($_SERVER["DOCUMENT_ROOT"].'/../app');
        }
        $this->applicationPath = $applicationPath;
        $this->configuration = Configuration::create($this->applicationPath);
    }

    public function configure() {
        $this->configuration->registerRoutes();
    }

    public function run() {
        $this->configure();
        \yapaf\DevServer::handle();
        $requestHandler = new RequestHandler($this->configuration);
        return $requestHandler->handle();
    }
}


