<?php

class Webicks_Parser_Router extends Webicks_Parser_Abstract {
	const INVALID_RULE = 1;
	
	private $_dataContainer;
	
	private $_parsedRules = array();
	private $_hasRun = false;
	
	
	public function load (&$data) {
//		var_dump($data);
		$this->_dataContainer = explode("\n",$data);
		
	}
	
	public function run() {
		$this->_hasRun = true;
		foreach($this->_dataContainer as $dataLine) {
			$this->_parseRule($dataLine);
		}
	}
	
	public function fetch() {
		if(!$this->_hasRun) {
			$this->run();
		}
		return serialize($this->_parsedRules);
	}
	
	private function _parseRule($ruleLine) {
		if(preg_match("/\s*\^?(?P<regexp>.*?)\s*=\s*(?P<destination>.*)$/",$ruleLine,$matches)) {
			$regexp = $matches['regexp'];
			$regexp = (!preg_match("/^![0-9]{3}$/", $regexp)) ? ("^" . $regexp . "$") : substr($regexp, 1);
			$this->_parsedRules[$regexp] = $matches['destination'];
		} else {
			throw new Exception("Invalid rule provided", Webicks_Parser_Router::INVALID_RULE);
		}
	}
	
}