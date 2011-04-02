<?php
namespace Webicks\Acl\Rules;

class AuthRule extends RulesAbstract {

    protected $matchString = '/^AUTH http\((?P<param_string>.*)\)$/';

    public function match($ruleString) {
        if(preg_match($this->matchString, $ruleString, $matches)) {
            return $this->validateAuth($matches['param_string']);
        } else {
            return false;
        }
    }

    private function validateAuth($paramString) {
        return false;
    }
}