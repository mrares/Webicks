<?php
namespace Webicks\Acl;
//function cidr_match($ip, $range)
//{
//    @list ($subnet, $bits) = split('/', $range);
//    if(!$bits) {$bits=32;} 				// In case the bitmask was omitted.
//    $ip = ip2long($ip);
//    $subnet = ip2long($subnet);
//    $mask = -1 << (32 - $bits);
//    $subnet &= $mask; 					// In case the supplied subnet wasn't correctly aligned
//    return ($ip & $mask) == $subnet;
//}

class Object {
	private $aclString;

	private $functions = array('IP'=>'matchIP');

	private $_filters = array();
	private $_stringFilters = array();

	private function parse($string) {

	}

	const MATCH_IP_RULE = 0x1;

	private function matchIP($addrString) {

		$ips = explode(', ', $addrString);
		foreach($ips as $cidrRange) {
			@list ($subnet, $bits) = split('/', $cidrRange);
			if(!$bits) ($bits = 32);
			$subnet = ip2long($subnet);
			$subnet &= (-1 <<(32 - $bits));
			$this->_filters[] = function() use ($subnet) {
				return ($_SERVER['REMOTE_ADDR'] & $subnet) == $subnet;
			};

			$this->_stringFilters[] = self::MATCH_IP_RULE . $subnet;
		}
	}

	public function __construct($aclString) {
		$matches = array();
		if(preg_match('/^(?P<function_name>[A-Z]+)\((?P<param_string>.*)\);$/', $aclString, $matches)) {
			if(array_key_exists($matches['function_name'], $this->functions)) {
				$filter = $this->functions[$matches['function_name']];
				call_user_func(array($this,$filter), $matches['param_string']);
			}
		}
	}

	public function validate() {
		$truth = false;
		foreach($this->_filters as $filter) {
			$truth = $truth || ($filter());
		}
		return $truth;
	}

	public function fetchCompiled() {
		return $this->_stringFilters;
	}

}