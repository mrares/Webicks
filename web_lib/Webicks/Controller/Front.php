<?php
namespace Webicks\Controller;


use Mach\Pattern\Singleton,
Webicks\Router,
Webicks\Acl,
Webicks\Acl\Rule,
Webicks\Controller\Exception\stopActionException,
Webicks\Request;

class Front extends Singleton {
    private $_isDispatched = false;
    private $_controllers = array();
    private $_stoppedDispatch = false;
    private $_stoppedRender = false;

    protected $router;
    protected $acl;
    protected $request;

    const ACTION_SUFFIX = 'Action';

    protected function __init(Router $router, Acl $acl, Request $request) {
        $this->router = $router;
        $this->acl = $acl;
        $this->request = $request;
        if($acl->verifyRequest($router->getDestination()) == Rule::RULE_DENY)
        {
            $this->router->setAction('deny');
        }
    }

    public function dispatch() {
        if($this->_isDispatched) {
            throw new \Exception('Request already dispatched');
        }
        $this->_isDispatched = true;
        $requestMethod = $this->request->getMethod();
        if(isset($this->_controllers[$requestMethod])) {
            $controllerClass = $this->_controllers[$requestMethod];
            $controller = new $controllerClass();
            if(method_exists($controller, 'preDispatch')) {
                $controller->preDispatch();
                if($this->_stoppedDispatch) {
                    return;
                }
            }
            $action = $this->router->getAction();
            $actionMethod = $action . self::ACTION_SUFFIX;
            try {
            $controller->$actionMethod();
            } catch(stopActionException $e) {}
            if(method_exists($controller, 'postDispatch')) {
                $controller->postDispatch();
                if($this->_stoppedRender) {
                    return;
                }
            }
        }
    }

    public function stopDispatch(){$this->_stoppedDispatch = true;}
    public function stopRender(){$this->_stoppedRender = true;}
    public function getRouter(){return $this->router;}
    public function getAcl(){return $this->acl;}
    public function getRequest(){return $this->request;}
    public function setRouter($router){$this->router = $router;}
    public function setAcl($acl){$this->acl = $acl;}
    public function setRequest($request){$this->request = $request;}
	public function registerControllers(array $controllers) {$this->_controllers = array_merge($this->_controllers, $controllers);}
    public function getRegisteredControllers() {return $this->_controllers;}
    public function isDispatched() {return $this->_isDispatched;}
}