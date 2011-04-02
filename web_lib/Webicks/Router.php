<?php
namespace Webicks;
/**
 * Webicks router class
 *
 * @author Rares Mirica
 * @version 0.1
 * @package Webicks
 *
 */
class Router extends \Mach\Pattern\Singleton {
	private $_available = false;
	private $_routed = false;

	const DEFAULT_URL_GLOBAL       = 'url';
	const DEFAULT_ROUTER_DOCUMENT  = '/.router';

	const REQ_DIRECTORY            = 'docDir';
	const REQ_DOCUMENT            = 'docFile';
	const REQ_ORIGIN               = 'origUrl';
	const REQ_DESTINATION          = 'destUrl';
	const REQ_STATUS               = 'status';

	const REQ_STATUS_200           = 200;
	const REQ_STATUS_404           = 404;
	const REQ_STATUS_500           = 500;
	const REQ_STATUS_UNKNOWN       = 0;

	const FILE_EXISTS 		= 0x1; //If file exists stop processing rules.
	const ALL_RULES_LAST 	= 0x2; //Treat first rule that matches as LAST. (Excludes CHAIN_RULES)
	const CHAIN_RULES 		= 0x4; //Chain rules, output of last matching as input to the next. (Excludes ALL_RULES_LAST)

	private $data = array();

	protected function __init($url = false, $flags = 0x0) {

		$this->data[self::REQ_ORIGIN]  = ($url!==false) ? $url : $_REQUEST[self::DEFAULT_URL_GLOBAL];
		$url = $this->data[self::REQ_ORIGIN];

		$this->data[self::REQ_DIRECTORY] = substr($url, 0, strrpos($url, '/'));
        $this->data[self::REQ_DOCUMENT] = substr($url, strrpos($url, '/'));

		if($router = Document::fetch($this->data[self::REQ_DIRECTORY].self::DEFAULT_ROUTER_DOCUMENT)) {
			$url = $this->data[self::REQ_DOCUMENT];
		} elseif($router = Document::fetch(self::DEFAULT_ROUTER_DOCUMENT)) {
			$url = $this->data[self::REQ_DIRECTORY] . $this->data[self::REQ_DOCUMENT];
		} else {
			$this->data[self::REQ_DESTINATION] = $url;
			$this->data[self::REQ_STATUS] = self::REQ_STATUS_UNKNOWN;
			return;
		}

		  $routing = unserialize($router->getContent());

            if(!is_array($routing)) {
               throw new Exception('WTF NO ARRAY');
            }

            $newDest = $url;
            foreach($routing as $route=>$destination) {
                $route = str_replace('/', '\/', $route);
                if($destination[0]!='/') {
                	$destination = $this->data[self::REQ_DIRECTORY] . '/' . $destination;
                }

                if (! $flags & self::CHAIN_RULES ) {
                	$newDest = $url;
                }

            	if($flags & self::FILE_EXISTS) {
                	if(Document::exists(trim($newDest))) {
                		//If current destination marks existing file, stop processing rules
                		break;
                	}
                }

                $newDest = preg_replace("/" . $route . "/", $destination, $newDest, - 1, $count);
                $newDest = str_replace('//', '/', $newDest);

                if($count && ($flags & self::ALL_RULES_LAST) && ($flags & self::CHAIN_RULES == 0x0)) {
                    break; // all rules are LAST
                }
            }

            if( ! Document::exists(trim($newDest))) {
                if(isset($routing['404'])) {
                    $this->_routed = true;
                    $newDest = $routing['404'];
                    $this->data[self::REQ_STATUS] = self::REQ_STATUS_404;
                }
                else {
                    $newDest = $this->data[self::REQ_ORIGIN];
                    $this->data[self::REQ_STATUS] = self::REQ_STATUS_UNKNOWN;
                }
            } else {
            	$this->_routed = true;
                $this->data[self::REQ_STATUS] = self::REQ_STATUS_200;
            }

            $this->data[self::REQ_DESTINATION] = trim($newDest);
	}

    public function getDestination() {
    	return $this->data[self::REQ_DESTINATION];
    }

    public function isStatus($status) {
    	return ($this->data[self::REQ_STATUS] === $status);
    }

    public function dump() {
    	header("Content-type: text/plain");
    	var_dump($this->data);
    	die();
    }
}