<?php
namespace Webicks;
use Mach\Pattern\Singleton;

class Request extends Singleton {

    const METHOD_GET     = 'GET';
    const METHOD_POST    = 'POST';
    const METHOD_PUT     = 'PUT';
    const METHOD_DELETE  = 'DELETE';

    protected $requestUri = null;
    protected $requestMethod = null;
    protected $requestData = array();

    protected function __init(array $requestData = array()) {
        $this->requestUri = $_SERVER['REQUEST_URI'];
        $this->requestMethod = $_SERVER['REQUEST_METHOD'];
        $this->requestData = array_merge($_GET, $_POST);
    }

    public function getParam($key, $default = null) {
        return (isset($this->requestData[$key])) ? $this->requestData[$key] : $default;
    }

    public function hasParam($ket) {
        return isset($this->requestData[$key]);
    }

    public function getAllPArams() {
        return $this->requestData;
    }

    public function getDestination() {
        return $this->requestUri;
    }

    public function getMethod() {
        return $this->requestMethod;
    }
}