<?php
namespace Webicks\Acl;

class Rule {

	const RULE_ALLOW = 'ALLOW';
	const RULE_DENY = 'DENY';

	const RULES_SUFFIX = 'Rule';

	private $action = null;
	private $context = null;

	public static $ruleClasses = array();

	private function parseAuthHTTP() {
		return false;
	}

	public function __construct($context, $action, $ruleString) {
	    $this->context = $context;
	    $matches = array();
	    foreach (self::$ruleClasses as $rule) {
	        $rule->setContext($this->context);
	        if($rule->match($ruleString)) {
	            $this->action = $action;
	        }
	    }
	}

	public static function registerRules(array $ruleClasses) {
	    foreach ($ruleClasses as $ruleClass) {
	        $className = $ruleClass.self::RULES_SUFFIX;
	        array_push(self::$ruleClasses, new $className());
	    }
	}

	public function getAction() {
		return $this->action;
	}

}