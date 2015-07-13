<?php
class DemoController extends \yapaf\Controller {
    public function hello() {
        $this->view->set('name', $this->request->get('name'));
    }
}
