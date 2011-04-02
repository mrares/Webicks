<?php
namespace Webicks\Parser;

abstract class AbstractParser
{
    const MIME_TYPE = 'MIME';
    const CONTENT = 'CONTENT';

    protected $_dataContainer = false;
    protected $_metaContainer = array();

    function load( &$data, $map ) {
        $this->_dataContainer = $data[$map[self::CONTENT]];

        foreach($map as $dataKey=>$mappedKey) {
            if(isset($data[$mappedKey])) {
                $this->_metaContainer[$dataKey] = $data[$mappedKey];
                unset($data[$mappedKey]);
            }
        }
    }

    public function fetch() {
        return $this->_dataContainer;
    }

    public function getMeta( $key = false ) {
        if($key) {
            return $this->_metaContainer[$key];
        }

        return $this->_metaContainer;
    }

    public function getDocumentFormatted() {
        return serialize(array('MIME'=>$this->getMeta(self::MIME_TYPE), 'content'=>$this->getData()));
    }

    public function getData() {
        return $this->_dataContainer;
    }

    abstract public function run();
}