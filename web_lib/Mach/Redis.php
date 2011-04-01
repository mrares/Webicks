<?php
class Mach_Redis extends Mach_Pattern_Singleton {
	private $_connection = false;
	
	protected function __init($dsn) {
		$matches = array ();
		if (! preg_match ( "/^redis:\/\/(?P<host>[^:|\/|\?]+)(?:(:(?P<port>[0-9]+))?(?:(\/\?timeout=(?P<timeout>[0-9.]+))|\/)?|\/)$/", $dsn, $matches )) {
			throw new Exception ( "Blah, something went wrong!" );
		}
		
		$this->_connection = new Redis ();
		if(isset($matches['timeout']) && (float)$matches['timeout'] != 0) {
			$connected = $this->_connection->connect ($matches['host'], $matches['port'], $matches['timeout']);
		} elseif (isset($matches['port']) && (int)$matches['port'] != 0) {
			$connected = $this->_connection->connect ($matches['host'], $matches['port']);
		} else {
			$connected = $this->_connection->connect ($matches['host']);
		}
		
		if(!$connected) {
			throw new Exception("Could not connect to Redis! ({$dsn})");
		}
	}
	
	public function set($key, $value, $ttl=false) {
		if($ttl) {
			return $this->_connection->setex($key, $ttl, $value); //Preferred function for expires.
		}
		
		return $this->_connection->set($key, $value);
	}
	
	public function get($key) {
		return $this->_connection->get($key);
	}
	
} 