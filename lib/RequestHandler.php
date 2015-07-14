<?php
namespace yapaf;
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
        $mainController = null;

        $className = ucfirst($request->getClassName()).'Controller';

        if(file_exists($this->controllerPath.'/'.$className.'.php')) {
            require_once($this->controllerPath.'/'.$className.'.php');
            $mainController = new $className($this->viewPath);
            $mainController->setRequest($request);
            $mainController->setResponse($response);
        }

        if (is_null($mainController)) {
            $response->set('Not found');
            $response->setStatus(404, 'Not Found');
            return $response->render();
        }

        $returnValue = $mainController->handle();
        if(!is_null($returnValue)){
            $response->add($returnValue);
        }
        return $response->render();
    }
}

