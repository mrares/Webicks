<?php

$redis = Webicks_Redis::getInstance("redis://127.0.0.1/");

function isValidPOST() {
	$accepted_mime = array('text/html', 'image/png', 'image/jpeg', 'application/javascript', 'text/css', 'text/plain');

	$valid = 1;
	$valid = $valid && isset($_POST['publish']) && $_POST['publish'] == 1;
	$valid = $valid && (isset($_POST['content']) && !empty($_POST['content'])) || (isset($_FILES['file']) && !empty($_FILES['file']['tmp_name']) && $_FILES['file']['error'] === UPLOAD_ERR_OK);
	$valid = $valid && isset($_POST['MIME']) && !empty($_POST['MIME']) && in_array($_POST['MIME'], $accepted_mime);

	return $valid;
}

if(isValidPOST()) {

	$parsed = Webicks_Parser::getInstance((isset($_POST['dest']) && !empty($_POST['dest'])) ? $_POST['dest'] : $_REQUEST['url']);


	header('Content-type: text/plain');

	if(!empty($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
		var_dump($_FILES);
		$_POST['content'] = file_get_contents($_FILES['file']['tmp_name']);
	}

	$dataMap = array(
		Webicks_Parser_Abstract::MIME_TYPE	=> 'MIME',
		Webicks_Parser_Abstract::CONTENT	=> 'content'
	);

	$parsed->loadData($_POST, $dataMap);

	//Run parser lazily.
	$parsed->run();

	$destination = (isset($_POST['dest']) && !empty($_POST['dest'])) ? $_POST['dest'] : $_REQUEST['url'];

	$redis->set( $destination, $parsed->getDocument());

	header('Location: '.$_REQUEST['url']);
	exit;
}

//Init router instance...
$router = Webicks_Router::getInstance($_REQUEST['url'], Webicks_Router::ALL_RULES_LAST | Webicks_Router::FILE_EXISTS);

//Init ACL instance
$acl = Webicks_Acl::getInstance($_REQUEST['url']);

if($document = Webicks_Document::fetch($_REQUEST['url'])) {
	if($acl->verifyRequest($_REQUEST['url']) == Webicks_Acl_Rule::RULE_DENY) {
		die('ACCESS DENIED');
	}
	header("Content-type: ".$document->getType());
	echo $document->getContent();
	exit;
}

if( $content = Webicks_Document::fetch($router->getDestination())) {
	if($acl->verifyRequest($router->getDestination()) == Webicks_Acl_Rule::RULE_DENY) {
		die('ACCESS DENIED');
	}

	header("Content-type: ".$content->getType());
	echo $content->getContent();
	exit;
} else {
//Show stupid upload form.
?>
<!DOCTYPE html>
<html>
<head>
<title>Upload info</title>
</head>
<body>
It sucks to be here...
<form action="" method="post" enctype="multipart/form-data">
<input type="hidden" name="publish" value="1" />
<input type="text" name="dest" id="dest" />
<select name="MIME">
<option value="text/html">HTML</option>
<option value="image/png">PNG Image</option>
<option value="image/jpeg">JPEG Image</option>
<option value="application/javascript">Javascript</option>
<option value="text/css">CSS File</option>
<option value="text/plain">Plain text</option>
</select>
<textarea rows="25" cols="80" name="content"></textarea>
<input type="file" name="file" id="file" />
<input type="submit" name="submit" value="send!">
</form>
</body>
</html>
<?php
}