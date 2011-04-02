<?php
namespace Webicks\Acl\Rules;

use Webicks\Acl\Lexer;

class FromRule extends RulesAbstract {
    protected $matchString = '/^FROM (?P<param_string>[a-zA-Z_0-9.]*)$/';

//    private $context = null;

    public function match($ruleString) {
        if(preg_match($this->matchString, $ruleString, $matches)) {
            return $this->validateFrom($matches['param_string']);
        } else {
            return false;
        }
    }

    public function validateFrom($paramString) {
        if($paramString == 'any') {
			return true;
		}
		if(array_key_exists($paramString, Lexer::$acls[$this->context])) {
			$validate = Lexer::$acls[$this->context][$paramString];
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
}