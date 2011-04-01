<?php 

class Webicks_Acl_Rule {
	
	const RULE_ALLOW = 'ALLOW';
	const RULE_DENY = 'DENY';
	
	private $action = null;
	private $context = null;
	
	private $rules = array(
		'/^FROM (?P<param_string>[a-zA-Z_0-9.]*)$/'=>'parseFROM',
		'/^AUTH http\((?P<param_string>.*)\)$/'=>'parseAuthHTTP'
	);
	
	private function parseFROM($paramString) {
		if($paramString == 'any') {
			return true;
		}
		if(array_key_exists($paramString, Webicks_Acl_Lexer::$acls[$this->context])) {
			$validate = Webicks_Acl_Lexer::$acls[$this->context][$paramString];
			if($validate->validate()) {
				return true;
			}
		} elseif (preg_match('/^[0-9]+\.[0-9]+\.[0-9]+\.[0-9]+$/', $paramString) && $_SERVER['REMOTE_ADDR'] == $paramString) {
			return true;
		} elseif ($ip = gethostbyname($paramString)) {
			if($_SERVER['REMOTE_ADDR'] == $ip) {
				return true;
			}
		}
		return false;
	}
	
	private function parseAuthHTTP() {
		return false;
	}
	
	public function __construct($context, $action, $ruleString) {
		$this->context = $context;
		$matches=array();
		foreach($this->rules as $rule=>$function) {
			if(preg_match($rule, $ruleString, $matches)) {
				if(call_user_func(array($this,$function), $matches['param_string'])) {
					$this->action = $action;
				}
			}
		}
	}
	
	public function getAction() {
		return $this->action;
	}
	
}