<?php
include 'system/common.inc.php';
if(!$_GET['sid']) encypt_result('-255');
$data = decypt_request();
$sid = intval($_GET['sid']);
$config = DB::fetch_first("SELECT * FROM mail WHERE sid='{$sid}'");
if(!$config) encypt_result('-254');
if(!$data[4]) encypt_result('-253');
if(inBlackList($client['url'])) exit('-254');
$headers  = "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: text/html;charset=utf-8\r\n";
$headers .= "Content-Transfer-Encoding: Base64\r\n";
$headers .= 'From: =?UTF-8?B?'.base64_encode($config['sender'])."?= <{$config[prefix]}@ikk.me>\r\n";
$data[3] .= $config['footer'];
$res = mail($data[1], '=?UTF-8?B?'.base64_encode($data[2]).'?=', base64_encode($data[3]), $headers, '-f system@ikk.me');
encypt_result(array('status' => ($res ? 'ok' : 'Fail!')));


?>