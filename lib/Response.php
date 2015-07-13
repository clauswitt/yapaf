<?php
namespace yapaf;
class Response {
    protected $output = array();
    protected $headers = array();
    protected $statusCode = 200;
    protected $statusMessage = 'OK';
    protected $httpVersion = '1.1';
    protected $contentType = 'text/html';
    protected $cacheResponse = false;
    protected $maxAge = 0;
    protected $private = true;
    protected $noCache = false;
    protected $noStore = false;
    protected $mustRevalidate = false;
    public $unWrittenHeaders = array();

    public function __construct() {
    }


    public function setCaching($allow=false, $maxAge=0, $private=true, $noCache=false, $noStore=false, $mustRevalidate=false) {
        $this->cacheResponse = $allow;
        $this->maxAge = $maxAge;
        $this->private = $private;
        $this->noCache = $noCache;
        $this->noStore = $noStore;
        $this->mustRevalidate = $mustRevalidate;
        return $this;
    }

    protected function setCachingHeader() {
        if($this->cacheResponse) {
            $cacheString = 'Cache-Control: ';
            $settings = array();
            if($this->maxAge>0) {
                $settings[] = 'max-age='.$this->maxAge;
            }
            if($this->private) {
                $settings[] = 'private';
            } else {
                $settings[] = 'public';
            }
            if($this->noCache) {
                $settings[] = 'no-cache';
            }
            if($this->noStore) {
                $settings[] = 'no-store';
            }
            if($this->mustRevalidate) {
                $settings[] = 'must-revalidate';
            }
            $cacheString .= implode(', ', $settings);
            $this->setHeader($cacheString);
        }
     }

    public function setStatus($code, $msg) {
        $this->statusCode = $code;
        $this->statusMessage = $msg;
    }
    public function set($output){
        $this->output = $output;
        return $this->output;
    }
    public function add($value) {
        $this->output[] = $value;
        return $this;
    }
    public function render() {
        $this->setHeaders();
        $out = '';
        if(!is_array($this->output)) return $this->output;
        foreach($this->output as $output) {
           $this->handleOutput($output, $out);
        }
        return $out;
    }

    public function addHeader($header) {
        $this->headers[] = $header;
    }

    protected function setHeaders() {
        $this->setStatusHeader();
        $this->setContentTypeHeader();
        $this->setCachingHeader();
        foreach($this->headers as $header) {
           if (substr(strtoupper($header), 0, 5) === 'HTTP/') throw new \Exception('Cannot set status header in response - use setStatus method instead');
           $this->setHeader($header);
        }
    }

    protected function setHeader($header) {
        header($header, true);
    }

    protected function setContentTypeHeader() {
        $this->setHeader('Content-Type: ' . $this->contentType);
    }

    public function setContentType($contentType) {
        $this->contentType = $contentType;
        return $this;
    }

    protected function setStatusHeader() {
        $this->setHeader('HTTP/'.$this->httpVersion.' ' .$this->statusCode.' ' .$this->statusMessage);
    }

    protected function handleOutput($output, &$out) {
        $out .= $output;
    }

    public function setBadRequest() {
        $this->setStatus(400, 'Bad Request');
        return $this;
    }

    public function setUnauthorized() {
        $this->setStatus(401, 'Unauthorized');
        return $this;
    }

    public function setForbidden() {
        $this->setStatus(403, 'Forbidden');
        return $this;
    }

    public function setNotFound() {
        $this->setStatus(404, 'Not Found');
        return $this;
    }

    public function setMethodNotAllowed() {
        $this->setStatus(405, 'Method not allowed');
        return $this;
    }
}
