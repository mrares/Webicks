<?php
namespace Webicks;

use Webicks\Controller\Exception\stopActionException;

abstract class ControllerAbstract {
    /**
     * Our request
     * @var Webicks\Request
     */
    protected $request = null;

    public function __construct() {
        $this->request = Request::getInstance();
        static::init();
    }

    private function _getParam($key, $default = null) {
        return $this->request->getParam($key, $default);
    }

    private function _getAllParams() {
        return $this->request->getAllPArams();
    }

    public abstract function init();

    protected function forward($action, $params = array()) {
        if(method_exists($this, $action)) {
            call_user_func_array(array($this, $action), $params);
        } else {
            throw new \Exception('Could not forward action, method '.$action.' not found.');
        }
        throw new stopActionException();
    }
}