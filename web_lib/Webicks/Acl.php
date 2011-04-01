<?php

class Webicks_Acl extends Mach_Pattern_Singleton {
	
	const DEFAULT_URL_GLOBAL       = 'url';
	const DEFAULT_ACL_DOCUMENT  = '/.acl';
	
	
	const REQ_DIRECTORY            = 'docDir';
	const REQ_DOCUMENT            = 'docFile';
	const REQ_ORIGIN               = 'origUrl';
	const REQ_DESTINATION          = 'destUrl';
	const REQ_STATUS               = 'status';
	
	protected $acl = null;	
	
	protected function __init($url, $flags = 0x0) {
		$this->data[self::REQ_ORIGIN]  = ($url!==false) ? $url : $_REQUEST[self::DEFAULT_URL_GLOBAL];
		$url = $this->data[self::REQ_ORIGIN];

		$this->data[self::REQ_DIRECTORY] = substr($url, 0, strrpos($url, '/'));
        $this->data[self::REQ_DOCUMENT] = substr($url, strrpos($url, '/'));
		
		if($acl = Webicks_Document::fetch($this->data[self::REQ_DIRECTORY].self::DEFAULT_ACL_DOCUMENT)) {
//			header('Content-type: text/plain');
			
			$myAcl = Webicks_Acl_Lexer::lex($acl->getContent());
			
//			var_dump('>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>');
//			
//			var_dump($this->verifyRequest('/.tada'));
//			
//			var_dump('>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>');
			
			
//			var_dump($acl->getContent());
//			die('diracl');
			//Per-directory ACL found and applied.
		
		} elseif($acl = Webicks_Document::fetch(self::DEFAULT_ACL_DOCUMENT)) {
			die('defacl');
			//No per-directory ACL, applying default ACL.
		
		
		} else {
//			die('noacl');
			//No ACL FOUND!
			return;
		}
        
        
	}
	
	public function verifyRequest($url = FALSE, $method = 'GET') {
		foreach (Webicks_Acl_Lexer::$rules[Webicks_Acl_Lexer::CONTEXT_DEFAULT] as $rule) {
			if(preg_match('/' . $rule['match'] . '/', $url)) {
				if($action = $rule['rule']->getAction()) {
					return $action;
				}
			}
		}

		if(!isset(Webicks_Acl_Lexer::$rules[$method])) {
			throw new Exception('JBANG');
		}
		
		foreach (Webicks_Acl_Lexer::$rules[$method] as $rule) {
			if(preg_match('/' . $rule['match'] . '/', $url)) {
				if($action = $rule['rule']->getAction()) {
					return $action;
				}
			}
		}
	}
}