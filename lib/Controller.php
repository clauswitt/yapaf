<?php
namespace yapaf;
class Controller
{
    protected $response;
    public $request;
    protected $view;
    protected $viewPath;

    public function __construct($viewPath) {
        $this->viewPath = $viewPath;
    }

    public function setResponse($response) {
        $this->response = $response;
    }
    public function setRequest($request) {
        $this->request = $request;
    }
    public function setView($view) {
        $this->view = $view;
    }

    public function handle() {
        $view = new View($this->viewPath, $this->request->getClassName(), $this);
        $view->setRequest($this->request);
        $view->setResponse($this->response);
        $this->setView($view);
        $action = $this->request->getMethodName();

        $result = $this->$action();
        if(!is_null($result)) return $result;
        return $view->render($action);
    }
}
