<?php
namespace Webicks\Acl\Rules;

abstract class RulesAbstract {

    protected $context = null;

    /**
     * Return match string
     */
    public abstract function match($ruleString);

    public function setContext($context) {
        $this->context = $context;
    }

//    public abstract function parse() {
//
//    }

}