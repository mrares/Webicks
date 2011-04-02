<?php
namespace Webicks;

class Document {

	private $_key = false;
	private $_raw = false;
	public $new = false;

	public function __construct($key) {
		if($this->_raw = unserialize(Redis::getInstance()->get($key))) {
//			return $this;
		} else {
			$this->new = true;
			$this->_key = $key;
//			throw new Exception("Could not get data from Redis");
		}
	}

	/**
	 * Document getter
	 *
	 * @param string $key
	 * @return Webicks_Document
	 */
	public static function fetch($key) {
		$doc = new self($key);
		if ($doc->new) {
			unset($doc);
			return false;
		}
		return $doc;
	}

	/**
	 * Document maker
	 *
	 * @param string $key
	 * @param bool $force [optional]
	 * @return Webicks_Document
	 */
	public static function make($key, $force = false) {
		$doc = new self($key);
		if(!$doc->new && !$force) {
			unset($doc);
			return false;
		} elseif ($force) {
			$doc->new = true;
		}
		return $doc;
	}

	/**
	 * Check if a document exists
	 *
	 * @param string $key
	 * @return bool
	 */
	public static function exists($key) {
		return Redis::getInstance()->exists($key);
	}

	public function getType() {
		return $this->_raw['MIME'];
	}

	public function setType($type, $force = FALSE ) {
		if(!$this->new && !$force) {
			return $this;
		}
		$this->_raw['MIME'] = $type;
		return $this;
	}

	public function getContent() {
		return $this->_raw['content'];
	}

	public function setContent( $content, $force = FALSE ) {
		if(!$this->new && !$force) {
			return $this;
		}
		$this->_raw['content'] = $content;
		return $this;
	}

	public function save( $force = false ) {
		if(!$this->new && !$force) {
			throw new Exception("This is not a new object");
		}
		Redis::getInstance()->set($this->_key, serialize($this->_raw));
	}

	public function __toString() {
		return $this->getContent();
	}

}