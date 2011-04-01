<?php

class Webicks_Parser_Acl extends Webicks_Parser_Abstract {

	public function run() {
		$acl = Webicks_Acl_Lexer::compile($this->_dataContainer);
	}
	
}