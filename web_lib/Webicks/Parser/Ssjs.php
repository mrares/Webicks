<?php
namespace Webicks\Parser;

class Ssjs extends AbstractParser {
	/* (non-PHPdoc)
     * @see Webicks_Parser_Abstract::getDocumentFormatted()
     */
    public function getDocumentFormatted() {
        return $this->getData();
    }

	/* (non-PHPdoc)
     * @see Webicks_Parser_Abstract::run()
     */
    public function run() {
    }

}