<?php
namespace Webicks\Acl;

class Lexer {

	private $lexed = null;
	public static $acls = array();
	public static $rules = array(self::CONTEXT_DEFAULT=>array());

	const REQUEST_GET = 'GET';
	const REQUEST_POST = 'POST';
	const CONTEXT_DEFAULT = 'DEFAULT';

	private function __construct($data) {
		$this->lexed = $data;
	}

	private static function tokenize($data) {
		$lexmap = array(
		'get' => 'GET',
		'post' => 'POST',
		'acl '=>'ACL ',
		' allow '=>' ALLOW ',
		' deny '=>' DENY ',
		'url'=>'URL',
		' from '=>' FROM ',
		' auth '=>' AUTH ',
		' ip'=>' IP');


		//Normalize ACL to ALL-CAPS
		$result = array();
		foreach ($data as &$line) {
			$line = str_replace(array_keys($lexmap), $lexmap, $line);
		}
		return $data;
	}

	private static function buildACL($context, $data) {
		$matches = array();
		$return = array();
		$context = self::CONTEXT_DEFAULT;
		foreach($data as $line) {
			if(preg_match('/^\[(?P<context>[a-zA-Z_]+)\]$/', $line, $matchesContext)) {
				$context = strtoupper($matchesContext['context']);
				if(isset(self::$rules[$context])) {
					self::$acls[$context] = array();
				}
				continue;
			}

			if(preg_match('/^ACL (?P<acl_name>[a-zA-Z_]+) (?P<acl_string>.*)/', $line, $matches)) {
				self::$acls[$context][$matches['acl_name']] = new Object($matches['acl_string']);
			}
		}
		return self::$acls;
	}

	private static function buildRules($data) {
		$matches = array();
		$matchesContext = array();
		$return = array();
		$context = self::CONTEXT_DEFAULT;
		foreach ($data as $line) {
			if(preg_match('/^\[(?P<context>[a-zA-Z_]+)\]$/', $line, $matchesContext)) {
				$context = strtoupper($matchesContext['context']);
				if(isset(self::$rules[$context])) {
					self::$rules[$context] = array();
				}
				continue;
			}

			if(preg_match('/^URL\[(?P<url_match>[^\]]*)\] (?P<action>ALLOW|DENY) (?P<param_string>.*);$/', $line, $matches)){
				$rule = new Rule($context, $matches['action'], $matches['param_string']);
				$rule = array('match'=>$matches['url_match'], 'rule'=>$rule);
				self::$rules[$context][] =  $rule;
			}
		}
		return self::$rules;
	}

	public static function compile($acl_content) {
		$data = array_diff(explode("\n", str_replace("\r", '', $acl_content)), array(""));
		$tokenized = self::tokenize($data);
//		var_dump($tokenized);
		$acls = self::buildACL('here', $tokenized);

		header('Content-type: text/plain');

//		var_dump($acls);

//		$compiledAcl = array();
	foreach ($acls as $contextKey=>$context) {
		foreach($context as $aclName=>$acl) {
			$compiledAcl[$contextKey][$aclName] = $acl->fetchCompiled();
		}
	}
//
//		var_dump($compiledAcl);

//		die('E O F ');


		$rules = self::buildRules($tokenized);


		return true;
	}

	public static function lex($acl_content) {
		$data = array_diff(explode("\n", str_replace("\r", '', $acl_content)), array(""));
		$tokenized = self::tokenize($data);
//		var_dump($tokenized);
		$acls = self::buildACL('here', $tokenized);

		$rules = self::buildRules($tokenized);
//		header('Content-type: text/plain');
//		var_dump($rules);
	}
}