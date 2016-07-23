<?php
error_reporting(0);
$parm_string = trim(pack('H*', $_POST['parm']));
$parm_string = authcode($parm_string, 'DECODE', 'Tieba Sign API - DEBUG');
if(!$parm_string) exit();
$data = unserialize($parm_string);
if(!$data[4]) exit();
//if(ver($data[4]) < ver('1.13.11.4')) exit('Unsupported version');
$headers  = "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: text/html;charset=utf-8\r\n";
$headers .= "Content-Transfer-Encoding: Base64\r\n";
$headers .= 'From: =?UTF-8?B?'.base64_encode('贴吧签到助手 - 开放平台')."?= <open_mail_api@ikk.me>\r\n";
$data[3] .= '<br><p style="font-size: 12px; color: #9f9f9f; text-align: right; border-top: 1px solid #dedede; padding: 20px 10px 0; margin-top: 25px;">此封邮件来自 <a href="http://www.kookxiang.com" style="color: gray; font-weight: bold;">贴吧签到助手 - 开放平台</a><br>KK Open Mail API v0.3, 2014 &copy; KK\'s Laboratory.</p>';
$res = mail($data[1], '=?UTF-8?B?'.base64_encode($data[2]).'?=', base64_encode($data[3]), $headers);
echo serialize(array('status' => ($res ? 'ok' : 'Fail!')));

function authcode($string, $operation = 'DECODE', $key = '', $expiry = 0) {
	$ckey_length = 4;
	$key = md5($key);
	$keya = md5(substr($key, 0, 16));
	$keyb = md5(substr($key, 16, 16));
	$keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';
	$cryptkey = $keya.md5($keya.$keyc);
	$key_length = strlen($cryptkey);
	$string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;
	$string_length = strlen($string);
	$result = '';
	$box = range(0, 255);
	$rndkey = array();
	for($i = 0; $i <= 255; $i++) {
		$rndkey[$i] = ord($cryptkey[$i % $key_length]);
	}
	for($j = $i = 0; $i < 256; $i++) {
		$j = ($j + $box[$i] + $rndkey[$i]) % 256;
		$tmp = $box[$i];
		$box[$i] = $box[$j];
		$box[$j] = $tmp;
	}
	for($a = $j = $i = 0; $i < $string_length; $i++) {
		$a = ($a + 1) % 256;
		$j = ($j + $box[$a]) % 256;
		$tmp = $box[$a];
		$box[$a] = $box[$j];
		$box[$j] = $tmp;
		$result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
	}
	if($operation == 'DECODE') {
		if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
			return substr($result, 26);
		} else {
			return '';
		}
	} else {
		return $keyc.str_replace('=', '', base64_encode($result));
	}
}
function ver($ver){
	list($mv, $y, $m, $d) = explode('.', $ver);
	return $mv*1000000 + $y*10000 + $m*100 + $d;
}

?>