<?php
namespace yapaf;
require_once 'Router.php';
require_once 'Response.php';
require_once 'Request.php';
require_once 'Controller.php';
class RequestHandler {
    protected $applicationPath;
    protected $controllerPath;

    public function __construct($applicationPath) {
        $this->applicationPath = $applicationPath;
        $this->controllerPath = $applicationPath .'/controllers';
    }

    public function handle() {
        $request = new Request();
        $response = new Response();
        $request = Router::route($request);

        $className = ucfirst($request->getClassName());
        $methodName = $request->getMethodName();

        require_once($this->controllerPath.'/'.$className.'.php');
        $mainController = new $className($request, $response);

        if (is_null($mainController)) {
            $response-set('Not found');
            $response->setStatus(404, 'Not Found');
        }
        $mainController->setRequest($request);
        $mainController->setResponse($response);
        $returnValue = $mainController->$methodName();
        if(!is_null($returnValue)){
            $response->add($returnValue);
        }
        return $response->render();
    }
}

