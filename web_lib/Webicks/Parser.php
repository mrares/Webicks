<?php
class Webicks_Parser extends Mach_Pattern_Singleton
{
    private $_parser = false;

    public function __init( $url ) {
        if(preg_match('/.*\.(?P<extension>[a-zA-Z]*)$/', $url, $matches)) {
            $parserClass = __class__ . '_' . ucfirst($matches['extension']);
            if(@class_exists($parserClass)) { //This WILL throw errors, not all extensions are parsed.
                $this->_parser = new $parserClass();
            }
            else {
                $this->_parser = new Webicks_Parser_Default();
            }
        } else {
        	$this->_parser = new Webicks_Parser_Default();
        }
    }
    
    public function run() 
    {
    	$this->_parser->run();
    }    
    
    public function loadData( &$data, $map ) {
    	if(!$this->_sanitize($data)) throw new Exception("Unsanitized data provided!");
    	return $this->_parser->load( $data, $map );
    }
    
    public function getData() {
    	return $this->_parser->fetch();
    }
    
    public function getMeta($key = false) {
		return $this->_parser->getMeta($key); 
    }
    
    public function getDocument() {
    	return $this->_parser->getDocumentFormatted();
    }
    
    private function _sanitize(&$data) {
    	return true;
    }
    
}