<?php
include 'system/common.inc.php';
if(!$_GET['sid']) encypt_result('-255');
$req = decypt_request();
$sid = intval($client['id']);
if(!$sid) exit('-255');
if(inBlackList($client['url'])) exit('-254');
$exists = DB::result_first("SELECT sid FROM sae_invite WHERE sid='{$sid}'");
if(!$exists) encypt_result(array('status' => 'not allowed'));

$postData = array(
	'api_key' => 'ДђТыДђТыДђТы',
	'sid' => $client['id'],
	'key' => $client['key'],
	'host' => $client['host'],
	'url' => $client['url']
);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://sae.api.ikk.me/add.php');
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_TIMEOUT, 5);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$data = curl_exec($ch);
curl_close($ch);
if($data != 'ok') encypt_result(array('status' => 'failure'));
encypt_result(array('status' => 'ok'));
?>