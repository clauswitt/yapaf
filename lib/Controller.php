<?php
namespace yapaf;
class Controller
{
    protected $response;
    protected $request;

    public function setResponse($response) {
        $this->response = $response;
    }
    public function setRequest($request) {
        $this->request = $request;
    }
}
