<?php
include 'system/common.inc.php';
$sid = $_GET['sid'];
if(!$_GET['sid']) encypt_result('-255');
$req = decypt_request();

if($client['key'] == 'BLOCKED') encypt_result(array('status' => 'blocked'));
if(inBlackList($client['url'])) encypt_result(array('status' => 'blocked'));

encypt_result(array('status' => 'ok'));
?>