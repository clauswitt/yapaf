<?php
namespace yapaf;
require_once 'Router.php';
require_once 'Response.php';
require_once 'Request.php';
require_once 'Controller.php';
class RequestHandler {
    protected $applicationPath;
    protected $controllerPath;
    protected $viewPath;

    public function __construct($applicationPath) {
        $this->applicationPath = $applicationPath;
        $this->controllerPath = $applicationPath .'/controllers';
        $this->viewPath = $applicationPath .'/views';
    }

    public function handle() {
        $request = new Request();
        $response = new Response();
        $request = Router::route($request);

        $className = ucfirst($request->getClassName()).'Controller';

        require_once($this->controllerPath.'/'.$className.'.php');
        $mainController = new $className($this->viewPath);

        if (is_null($mainController)) {
            $response-set('Not found');
            $response->setStatus(404, 'Not Found');
        }
        $mainController->setRequest($request);
        $mainController->setResponse($response);
        $returnValue = $mainController->handle();
        if(!is_null($returnValue)){
            $response->add($returnValue);
        }
        return $response->render();
    }
}

