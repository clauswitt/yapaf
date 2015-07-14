<?php
namespace yapaf;
class Configuration {
    protected $applicationPath;
    protected $settings;
    protected $routes;
    private static $instance;
    private $request;
    private $response;
    private function __construct($applicationPath) {
        $this->applicationPath = $applicationPath;
        $this->loadSettings();
        $this->routes = $this->settings['routes'];
    }

    static function getInstance() {
        if(is_null($this::$instance)) throw new \Exception("Configuration object not created");
        return self::$instance;
    }

    static function create($applicationPath) {
        self::$instance = new self($applicationPath);
        return self::$instance;
    }

    private function loadSettings() {
        if(file_exists($this->applicationPath . '/configuration/settings.json')) {
            $this->settings = json_decode(file_get_contents($this->applicationPath . '/configuration/settings.json'), true);
        } elseif(file_exists($this->applicationPath . '/configuration/settings.php')) {
            $this->settings = include $this->applicationPath . '/configuration/settings.php';
        }
    }

    public function getRequest() {
        if(is_null($this->request)) $this->request = new Request;
        return $this->request;
    }

    public function getResponse() {
        if(is_null($this->response)) $this->response = new Response;
        return $this->response;
    }

    public function getSetting($settingPath, $defaultValue=null) {
        $settingPathArray = explode('.', $settingPath);
        $currentNode = $this->settings;
        foreach($settingPathArray as $part) {
            if(array_key_exists($part,$currentNode)) {
                if(is_array($currentNode[$part])) {
                    $currentNode = $currentNode[$part];
                } else {
                    return $currentNode[$part];
                }
            } else {
                break;
            }
        }
        return $defaultValue;
    }

    public function getControllerPath() {
        return $this->getSetting('paths.controllers',$this->applicationPath . '/controllers' );
    }

    public function getViewPath() {
        return $this->getSetting('paths.views',$this->applicationPath . '/views' );
    }

    public function getController() {
        $mainController = null;
        $className = ucfirst($this->request->getClassName()).'Controller';
        if(file_exists($this->getControllerPath().'/'.$className.'.php')) {
            require_once($this->getControllerPath().'/'.$className.'.php');
            $mainController = new $className($this->getViewPath());
            $mainController->setRequest($this->getRequest());
            $mainController->setResponse($this->getResponse());
        }
        return $mainController;

    }

    public function getApplicationPath() {
        return $this->applicationPath;
    }

    public function setRoutes($routes) {
        $this->routes = $routes;
    }

    public function getRoutes() {
        return $this->routes;
    }

    public function registerRoutes() {
        $GLOBALS['routes'] = $this->routes;
    }

}
