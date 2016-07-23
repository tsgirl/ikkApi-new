<?php
include 'system/common.inc.php';
if(!$_GET['sid']) encypt_result('-255');
$req = decypt_request();
encypt_result('0');
?>