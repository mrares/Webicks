<?php
namespace Webicsks\Parser;
use Webicks\Acl\Lexer;

class Acl extends AbstractParser {

	public function run() {
		$acl = Lexer::compile($this->_dataContainer);
	}

}