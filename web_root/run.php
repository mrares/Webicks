<?php

//var_dump($_SERVER);
//die();

//var_dump($_REQUEST);
//var_dump($_FILES);
//die();

//$redis = Mach_Redis::getInstance("redis://127.0.0.1");
//$redis = Mach_Redis::getInstance("redis://127.0.0.1/");
//$redis = Mach_Redis::getInstance("redis://127.0.0.1:124");
//$redis = Mach_Redis::getInstance("redis://127.0.0.1:124/");
//$redis = Mach_Redis::getInstance("redis://127.0.0.1/?timeout=2");
//$redis = Mach_Redis::getInstance("redis://127.0.0.1:124/?timeout=2");
$redis = Mach_Redis::getInstance("redis://127.0.0.1/");

function isValidPOST() {
	$accepted_mime = array('text/html', 'image/png', 'image/jpeg', 'application/javascript', 'text/css');
	
	$valid = 1;
	$valid = $valid && isset($_POST['publish']) && $_POST['publish'] == 1;
	$valid = $valid && (isset($_POST['content']) && !empty($_POST['content'])) || (isset($_FILES['file']) && !empty($_FILES['file']['tmp_name']) && $_FILES['file']['error'] === UPLOAD_ERR_OK);
	$valid = $valid && isset($_POST['MIME']) && !empty($_POST['MIME']) && in_array($_POST['MIME'], $accepted_mime);
	
	return $valid;
}

if(isValidPOST()) {

	if(!empty($_POST['source_url'])) {
		
	}
	
//	if($_REQUEST['MIME']=='image/jpeg') {
//		$_POST['content'] = base64_decode($_POST['content']);
//	}

	if(!empty($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
		var_dump($_FILES);
		$_POST['content'] = file_get_contents($_FILES['file']['tmp_name']);
	}
	
	$redis->set($_REQUEST['url'], serialize(array('MIME'=>$_POST['MIME'],'content'=>$_POST['content'])));
	die();
} elseif($content = unserialize($redis->get($_REQUEST['url']))) {
	header("Content-type: ".$content['MIME']);
	echo $content['content'];
	exit;
} else {
?>
<!DOCTYPE html>
<html>
<head>
<title>Upload info</title>
</head>
<body>
<form action="" method="post" enctype="multipart/form-data">
<input type="hidden" name="publish" value="1" />
<select name="MIME">
<option value="text/html">HTML</option>
<option value="image/png">PNG Image</option>
<option value="image/jpeg">JPEG Image</option>
<option value="application/javascript">Javascript</option>
<option value="text/css">CSS File</option>
</select>
<textarea rows="25" cols="80" name="content"></textarea>
<input type="file" name="file" id="file" />
<input type="submit" name="submit" value="send!">
</form>
</body>
</html>
<?php 
}