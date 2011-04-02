<?php
namespace Controller;
use Webicks\Parser\AbstractParser;

use Webicks\Redis;
use Webicks\Parser;

class postController extends \Webicks\ControllerAbstract {

    public function indexAction() {
        //Do nothing, all is done upon init :D
    }

    public function init() {
        if(!$this->isValidPost($this->request)) {
            return;
        }
        $this->handleContentPosting($this->request);
        die('HERE!');
    }

    private function handleContentPosting(\Webicks\Request $request) {
    	$parsed = Parser::getInstance((isset($_POST['dest']) && !empty($_POST['dest'])) ? $_POST['dest'] : $_REQUEST['url']);

    	header('Content-type: text/plain');

    	if(!empty($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
    		$_POST['content'] = file_get_contents($_FILES['file']['tmp_name']);
    	}

    	$dataMap = array(
    		AbstractParser::MIME_TYPE	=> 'MIME',
    		AbstractParser::CONTENT	=> 'content'
    	);

    	$parsed->loadData($_POST, $dataMap);

    	//Run parser lazily.
    	$parsed->run();

    	$destination = (isset($_POST['dest']) && !empty($_POST['dest'])) ? $_POST['dest'] : $_REQUEST['url'];

    	Redis::getInstance()->set( $destination, $parsed->getDocument());
    	echo 'OK!';
    	exit;
    }

    private function isValidPost(\Webicks\Request $request) {
    	$accepted_mime = array('text/html', 'image/png', 'image/jpeg', 'application/javascript', 'text/css', 'text/plain');

    	if($request->getParam('publish', false) != 1) {
    	    return false;
    	}
    	if($content = $request->getParam('content', null)) {
    	    if(empty($content)) {
    	        return false;
    	    }
    	}
    	if($_FILES['file']) {
    	    if(!empty($_FILES['file']['tmp_name'])) {
        	    if($_FILES['file']['error'] !== UPLOAD_ERR_OK) {
        	        return false;
        	    }
    	    }
    	}
    	if(!in_array($request->getParam('MIME', false), $accepted_mime)) {
    	    return false;
    	}

    	return true;
    }
}