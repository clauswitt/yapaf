<?php
class DemoController extends \yapaf\Controller {
    public function inAction() {
        $this->response->set('test ok: name is ' . $this->request->get('name'));
    }
}
