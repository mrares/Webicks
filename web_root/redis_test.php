<?php
$redis = new Redis(); $redis->connect("127.0.0.1
");

echo $redis->get($_GET['what']);
exit;