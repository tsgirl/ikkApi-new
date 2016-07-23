<?php
include 'system/common.inc.php';
if(!$_GET['sid']) encypt_result('-255');
$req = decypt_request();
if($client['key'] == 'BLOCKED') exit('-254');
$sid = $client['id'];
$url = addslashes($req[1]);
$url = str_replace('admin.php/', '', $url);
$host_hash = get_host_hash($url);
if(!$host_hash) exit('-255');
if(inBlackList($url)) exit('-254');
DB::query("UPDATE client SET host='{$host_hash}', url='{$url}' WHERE id='{$sid}'");
encypt_result(array('status' => 'ok'));
?>