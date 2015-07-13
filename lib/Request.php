<?php
namespace yapaf;
class Request extends \ArrayObject {
    /**
     * Instance parameters
     * @var array
     */
    protected $_params = array();
    //These are used for test purposes.
    //It is a bit of a hack, but php://input cannot be mocked/stubbed
    public $test = false;
    public $testRequestBody = '';
    public $testUrl = '';
    public function __construct() {
        if ($this->isCli()) {
            $this->_params = $this->parseArgs($_SERVER['argv']);
        }
        parent::__construct();
    }

    public function offsetSet($index, $newval) {
        return $this->set($index, $newval);
    }

    public function offsetGet($index) {
        return $this->get($index);
    }

    public function offsetExists($index) {
        return $this->has($index);
    }

    public function offsetUnset($index) {
        return null;
    }


    public function getClassName() {
        return $this->_params['controller'];
    }

    public function getMethodName() {
        return $this->_params['action'];
    }

    public function getResponseMethod() {
        return $this->_params['method'];
    }

    /**
     *
     * @see http://msdn.microsoft.com/en-us/library/system.web.httprequest.item.aspx
     * @param string $key
     * @return mixed
     */
    public function __get($key) {
        switch (true) {
            case isset($this->_params[$key]):
                return $this->_params[$key];
            case isset($_POST[$key]):
                return $_POST[$key];
            case isset($_GET[$key]):
                return $_GET[$key];
            case isset($_COOKIE[$key]):
                return $_COOKIE[$key];
            case ($key == 'REQUEST_URI'):
                return $this->getRequestUri();
            case ($key == 'PATH_INFO'):
                return $this->getPathInfo();
            case isset($_SERVER[$key]):
                return $_SERVER[$key];
            case isset($_ENV[$key]):
                return $_ENV[$key];
            default:
                return null;
        }
    }

    public function getRequestUri() {
        if($this->test===true) return $this->testUrl;
        return $_SERVER['REQUEST_URI'];

    }

    /**
     * Alias to __get
     *
     * @param string $key
     * @return mixed
     */
    public function get($key) {
        return $this->__get($key);
    }

    public function __set($key, $value) {
        $this->_params[$key] = $value;
    }

    /**
     * Alias to __set()
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function set($key, $value) {
        return $this->__set($key, $value);
    }

    /**
     * Check to see if a property is set
     *
     * @param string $key
     * @return boolean
     */
    public function __isset($key) {
        switch (true) {
            case isset($this->_params[$key]):
                return true;
            case isset($_POST[$key]):
                return true;
            case isset($_GET[$key]):
                return true;
            case isset($_COOKIE[$key]):
                return true;
            case isset($_SERVER[$key]):
                return true;
            case isset($_ENV[$key]):
                return true;
            default:
                return false;
        }
    }

    /**
     * Alias to __isset()
     *
     * @param string $key
     * @return boolean
     */
    public function has($key) {
        return $this->__isset($key);
    }

    /**
     * Retrieve a member of the $_GET superglobal
     *
     * If no $key is passed, returns the entire $_GET array.
     *
     * @todo How to retrieve from nested arrays
     * @param string $key
     * @param mixed $default Default value to use if key not found
     * @return mixed Returns null if key does not exist
     */
    public function getQuery($key = null, $default = null) {
        if (null === $key) {
            return $_GET;
        }

        return (isset($_GET[$key])) ? $_GET[$key] : $default;
    }

    /**
     * Retrieve a member of the $_POST superglobal
     *
     * If no $key is passed, returns the entire $_POST array.
     *
     * @todo How to retrieve from nested arrays
     * @param string $key
     * @param mixed $default Default value to use if key not found
     * @return mixed Returns null if key does not exist
     */
    public function getPost($key = null, $default = null) {
        if (null === $key) {
            return $_POST;
        }

        return (isset($_POST[$key])) ? $_POST[$key] : $default;
    }

    /**
     * Retrieve a member of the $_COOKIE superglobal
     *
     * If no $key is passed, returns the entire $_COOKIE array.
     *
     * @todo How to retrieve from nested arrays
     * @param string $key
     * @param mixed $default Default value to use if key not found
     * @return mixed Returns null if key does not exist
     */
    public function getCookie($key = null, $default = null) {
        if (null === $key) {
            return $_COOKIE;
        }

        return (isset($_COOKIE[$key])) ? $_COOKIE[$key] : $default;
    }

    /**
     * Retrieve a member of the $_SERVER superglobal
     *
     * If no $key is passed, returns the entire $_SERVER array.
     *
     * @param string $key
     * @param mixed $default Default value to use if key not found
     * @return mixed Returns null if key does not exist
     */
    public function getServer($key = null, $default = null) {
        if (null === $key) {
            return $_SERVER;
        }

        return (isset($_SERVER[$key])) ? $_SERVER[$key] : $default;
    }

    /**
     * Retrieve a member of the $_ENV superglobal
     *
     * If no $key is passed, returns the entire $_ENV array.
     *
     * @param string $key
     * @param mixed $default Default value to use if key not found
     * @return mixed Returns null if key does not exist
     */
    public function getEnv($key = null, $default = null) {
        if (null === $key) {
            return $_ENV;
        }

        return (isset($_ENV[$key])) ? $_ENV[$key] : $default;
    }

    /**
     * Return the method by which the request was made
     *
     * @return string
     */
    public function getMethod() {
        return $this->getServer('REQUEST_METHOD');
    }

    /**
     * Was the request made by POST?
     *
     * @return boolean
     */
    public function isPost() {
        if ('POST' == $this->getMethod()) {
            return true;
        }

        return false;
    }

    /**
     * Was the request made by GET?
     *
     * @return boolean
     */
    public function isGet() {
        if ('GET' == $this->getMethod()) {
            return true;
        }

        return false;
    }

    /**
     * Was the request made by PUT?
     *
     * @return boolean
     */
    public function isPut() {
        if ('PUT' == $this->getMethod()) {
            return true;
        }

        return false;
    }

    /**
     * Was the request made by DELETE?
     *
     * @return boolean
     */
    public function isDelete() {
        if ('DELETE' == $this->getMethod()) {
            return true;
        }

        return false;
    }

    /**
     * Was the request made by HEAD?
     *
     * @return boolean
     */
    public function isHead() {
        if ('HEAD' == $this->getMethod()) {
            return true;
        }

        return false;
    }

    /**
     * Was the request made by OPTIONS?
     *
     * @return boolean
     */
    public function isOptions() {
        if ('OPTIONS' == $this->getMethod()) {
            return true;
        }

        return false;
    }


    public function isCli() {
        if (php_sapi_name() == 'cli' && empty($_SERVER['REMOTE_ADDR'])) {
            return true;
        }
        else {
            return false;
        }
    }

    public function parseArgs($argv) {
        array_shift($argv);
        $out = array();
        foreach ($argv as $arg) {
            if (substr($arg, 0, 2) == '--') {
                $eqPos = strpos($arg, '=');
                if ($eqPos === false) {
                    $key = substr($arg, 2);
                    $out[$key] = isset($out[$key]) ? $out[$key] : true;
                }
                else {
                    $key = substr($arg, 2, $eqPos - 2);
                    $out[$key] = substr($arg, $eqPos + 1);
                }
            }
            else if (substr($arg, 0, 1) == '-') {
                if (substr($arg, 2, 1) == '=') {
                    $key = substr($arg, 1, 1);
                    $out[$key] = substr($arg, 3);
                }
                else {
                    $chars = str_split(substr($arg, 1));
                    foreach ($chars as $char) {
                        $key = $char;
                        $out[$key] = isset($out[$key]) ? $out[$key] : true;
                    }
                }
            }
            else {
                $out[] = $arg;
            }
        }
        return $out;
    }

    protected $requestBody = '';
    protected $requestJson = array();

    public function getBody() {
        if($this->requestBody==='') {
            if($this->test===true) {
                return $this->testRequestBody;
            }
            $this->requestBody = file_get_contents('php://input');
        }
        return $this->requestBody;
    }

    public function getJson() {
        if(empty($this->requestJson)) {
            $this->requestJson = json_decode($this->getBody(), true);
        }
        return $this->requestJson;
    }
}
