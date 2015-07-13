<?php
namespace yapaf;
class View {
    protected $variables = array();
    protected $controllerName;
    protected $controllerObject;
    /**
     *
     * @var ConfigurationManager
     */
    protected $action;
    protected $rootPath;
    protected $helpers;
    protected $response;
    protected $request;
    protected $_rendered;

    function __construct($viewPath, $controllerName, $controllerObject) {
        $this->viewPath = $viewPath;
        $this->controllerName = $controllerName;
        $this->controllerObject = $controllerObject;
        $this->_rendered = false;
    }

    public function setResponse($response) {
        $this->response = $response;
    }

    public function setRequest($request) {
        $this->request = $request;
    }

    public function isRendered() {
        return $this->_rendered;
    }

    /**
     * Render the view.
     * @param $methodName
     * @return unknown_type
     */
    public function render($methodName = "", $isSub = false, $forceRender = false) {
        $format = 'html';
        if ($this->request->has('format')) {
            $format = $this->request['format'];
            if (($format != 'json')) {
                $format = 'html';
            }
        }
        $this->_rendered = true;
        return $this->handleFormat($methodName, $format, $isSub, $forceRender);
    }

    public function handleFormat($methodName = '', $format = 'html', $isSub = false, $forceRender = false) {
        if ($isSub && (ucfirst($format) != 'Html') && !$forceRender) {
            return $this->handleSubRendering($methodName);
        }
        $renderMethod = 'as' . ucfirst($format);
        if (method_exists($this, $renderMethod)) {
            return $this->$renderMethod($methodName);
        }
        return $this->asHtml($methodName);
    }

    public function handleSubRendering($methodName = '') {
        return $this->variables[$methodName];
    }

    public function renderPartial($partialName, $arguments=array(), $controllerName='') {
        if($controllerName=='') {
            $controllerName = $this->controllerName;
        }

        extract($arguments);
        ob_start();
        $viewpath = $this->getPartialPath($partialName, $controllerName);
        include($viewpath);
        $out = ob_get_clean();
        return $out;
    }

    private function getPartialPath($name, $controllerName) {
        $filename = $this->viewPath . '/' . $controllerName . '/' . 'partials' . '/' . $name . '.php';
        if (file_exists($filename))
            return $filename;
        $filename = $this->viewPath . '/' . $controllerName . '/' . $name . '.php';
        if (file_exists($filename))
            return $filename;
        $filename = $this->viewPath . '/' . 'partials' . '/' . $name . '.php';
        if (file_exists($filename))
            return $filename;
        $filename = $this->viewPath . '/' . 'partials' . '/' . $controllerName . '/' . $name . '.php';
        if (file_exists($filename))
            return $filename;
        return '';
    }

    public function asHtml($methodName = "") {
        $this->response->setContentType('text/html');
        if ($methodName == "") {
            $bt = debug_backtrace();
            $methodName = $bt[1]['function'];
        }
        $this->actionName = $methodName;
        $this->set('controller', $this->controllerObject, $this->actionName);
        $viewpath = $this->viewPath . '/' . $this->controllerName . '/' . $this->actionName . '.php';
        if(!file_exists($viewpath)) return '';
        extract($this->variables[$this->actionName]);
        ob_start();
        include($viewpath);
        $out = ob_get_clean();

        return $out;
    }

    /**
     * Return view variables as json string
     * @return unknown_type
     */
    public function asJson($methodName = "") {
        $this->response->setContentType('application/json');
        if ($methodName == "") {
            $bt = debug_backtrace();
            $methodName = $bt[1]['function'];
        }
        $this->actionName = $methodName;
        $json = json_encode($this->variables[$this->actionName]);
        $request = ConfigurationManager::getInstance()->getRequest();
        if ($request->has('jsonpMethod')) {
            $json = $request->get('jsonpMethod') . '(' . $json . ')';
        }
        return $json;
    }

    /**
     * Set the named variable with the value
     * @param $name
     * @param $value
     * @return unknown_type
     */
    public function set($name, $value, $methodName = '') {
        if ($methodName == "") {
            $bt = debug_backtrace();
            $methodName = $bt[1]['function'];
        }
        $this->actionName = $methodName;
        $this->variables[$this->actionName][$name] = $value;
    }

    public function setVariables($arr, $methodName = '') {
        if ($methodName == "") {
            $bt = debug_backtrace();
            $methodName = $bt[1]['function'];
        }
        foreach($arr as $key=>$value) {
            $this->set($key, $value, $methodName);
        }
    }

    public function setHelper($name, $object, $methodName = '') {
        if ($methodName == "") {
            $bt = debug_backtrace();
            $methodName = $bt[1]['function'];
        }
        $this->actionName = $methodName;
        $this->helpers[$name] = $object;
    }

    public function getHelper($name) {
        return $this->helpers[$name];
    }

    public function __get($name) {
        if (array_key_exists($name, $this->variables[$this->actionName])) {
            return $this->variables[$this->actionName][$name];
        }
        if (array_key_exists($name, $this->variables[$this->actionName])) {
            return $this->variables[$this->actionName][$name];
        }
    }

    public function __call($name, $arguments) {
        $arrCaller = Array($this->controllerName, $name);
        $rval = call_user_func_array($arrCaller, $arguments);
        return $rval;
    }


}
