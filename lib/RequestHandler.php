<?php
namespace yapaf;
class RequestHandler {
    protected $applicationPath;
    protected $controllerPath;
    protected $viewPath;

    public function __construct($configuration) {
        $this->configuration = $configuration;
    }

    public function handle() {
        $request = $this->configuration->getRequest();
        $response = $this->configuration->getResponse();
        $request = Router::route($request);

        $mainController = $this->configuration->getController();

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

