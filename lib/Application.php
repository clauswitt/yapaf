<?php
namespace yapaf;
class Application
{
    protected $rootPath;
    protected $applicationPath;
    protected $publicPath;
    public function __construct($rootPath, $applicationPath, $publicPath) {
        $this->rootPath = $rootPath;
        $this->applicationPath = $applicationPath;
        $this->publicPath = $publicPath;
    }

    public function run() {
        return 'test ok';
    }
}


