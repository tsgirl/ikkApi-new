<?php
include 'system/common.inc.php';
$url = authcode(pack('H*', $_REQUEST['url']), 'DECODE', 'CLOUD-REGISTER');
$host_hash = addslashes(get_host_hash($url));
$url = addslashes($url);
if(!$host_hash) exit('-255');
if(inBlackList($url)) exit('-254');
$key = random(32);
$sid = DB::insert('client', array(
	'key' => $key,
	'host' => $host_hash,
	'url' => $url,
));
exit("1\t{$sid}\t{$key}");

?>