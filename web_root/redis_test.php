<?php

header("Content-type: text;");
$redis = new Redis(); $redis->connect("127.0.0.1");
if(!isset($_GET['what'])) {
    die('Please specify a key to get in "what"');
}
echo $redis->get($_GET['what']);
exit;