<?php
include 'system/common.inc.php';
if(!$_GET['sid']) throw new Exception('Illegal API request.');
if(in_array($_GET['sid'], $blacklist)) throw new Exception('Illegal API request.');
$_POST['parm'] = $_GET['parm'];
list($username, $password, $formhash) = decypt_request();
if($client['key'] == 'BLOCKED') showmessage('该网站在黑名单中，禁止使用此功能<br></p><ins>可能的原因：该网站非法盗取用户数据或篡改程序版权或将本软件用于商业用途</ins><br><p style="display: none">', 'http://www.kookxiang.com', 7);
if(inBlackList($client['url'])) showmessage('该网站在黑名单中，禁止使用此功能<br></p><ins>可能的原因：该网站非法盗取用户数据或篡改程序版权或将本软件用于商业用途</ins><br><p style="display: none">', 'http://www.kookxiang.com', 7);
if(!$username || !$password || !$formhash) throw new Exception('Illegal package');
if(!$client['url']) header('Location: http://www.kookxiang.com/thread-2022-1-1.html');
if($_POST['cookie']) prase_post_cookie();
/*
if(!$_POST['token']){
	// 得到UID
	fetch('https://passport.baidu.com/v2/api/?getapi&tpl=tb&apiver=v3&tt='.time().'520&class=login&logintype=dialogLogin&callback=bd__cbs__sbw');
	// 得到token
	$token_query = fetch('https://passport.baidu.com/v2/api/?getapi&tpl=tb&apiver=v3&tt='.time().'520&class=login&logintype=dialogLogin&callback=bd__cbs__sbw');
	preg_match('/"token" : "(\w+)"/', $token_query, $match);
	$token = $match[1];
}else{
	$token = $_POST['token'];
}
*/

//-------------------
function curl_post_tsgirl($pda, $url) {
    $header = array(
        "Content-Type: application/x-www-form-urlencoded"
    );
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $pda);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    $re = curl_exec($ch);
    curl_close($ch);
    return $re;
}
$un = $username;
$fileType = mb_detect_encoding($un, array(
    'UTF-8',
    'GBK',
    'LATIN1',
    'BIG5'
));
if ($fileType != 'UTF-8') {
    // echo 'Encodeing changed to UTF-8.</br>';
    $un = mb_convert_encoding($un, 'utf-8', $fileType);
}
if($_POST['verifycode']!='' && $_POST['codestring']!=''){
//echo 'verify is true';
$data = array(
    '_client_id=wappc_1469251281859_249',
    '_client_type=2',
    '_client_version=6.0.1',
    '_phone_imei=352069051699600',
    'apid=sw',
    'channel_id=',
    'channel_uid=',
    'cuid=7A44C80D36CBAFFA0F4B67FAF78F4810|306158050127353',
    'from=tieba',
    'isphone=0',
    'model=H701',
    'passwd=' . base64_encode($password) ,
    'stErrorNums=0',
    'stMethod=1',
    'stMode=1',
    'stSize=64',
    'stTime=940',
    'stTimesNum=0',
    'timestamp=' . time() . '231',
    'un=' . $un,
    'vcode='.$_POST['verifycode'],
    'vcode_md5='.$_POST['codestring'],
    );
}else{
$data = array(
    '_client_id=wappc_1469251281859_249',
    '_client_type=2',
    '_client_version=6.0.1',
    '_phone_imei=352069051699600',
    'apid=sw',
    'channel_id=',
    'channel_uid=',
    'cuid=7A44C80D36CBAFFA0F4B67FAF78F4810|306158050127353',
    'from=tieba',
    'isphone=0',
    'model=H701',
    'passwd=' . base64_encode($password) ,
    'stErrorNums=0',
    'stMethod=1',
    'stMode=1',
    'stSize=64',
    'stTime=940',
    'stTimesNum=0',
    'timestamp=' . time() . '231',
    'un=' . $un
);
}
//--add tiebaclient signature---
$data = implode("&", $data) . "&sign=" . strtoupper(md5(implode("", $data) . "tiebaclient!!!"));
//-----------------------------
//$data=implode('&',$data);
//echo $data;
//echo '<br>';
$rc = curl_post_tsgirl($data, 'http://c.tieba.baidu.com/c/s/login');
//echo $rc;
$re=json_decode($rc);
$result=$re->error_code;
/*
//-------------------
$data = array();
$data['un'] = urlencode($username);
$data['timestamp'] = time().'231';
$data['passwd'] = base64_encode($password);
$data['vcode'] = $_POST['verifycode'];
$data['vcode_md5'] = $_POST['codestring'];
//--add tiebaclient signature---
        $pda = array(
            '_client_id' => 'wappc_1469251281859_249',
            '_client_type' => '2',
            '_client_version' => '6.0.1',
            '_phone_imei' => '352069051699600',
        ) + $data;
    $data = implode("&", $pda)."&sign=".md5(implode("", $pda)."tiebaclient!!!");
//-----------------------------
// 尝试登录
$data_str = '';
foreach($data as $key => $value){
	$data_str .= "{$key}={$value}&";
}
$result = fetch('http://c.tieba.baidu.com/c/s/login', $data_str);
*/
if($result!='0'){
	if($result=='5' || $result=='6'){
		$verifyAddress = $re->anti->vcode_md5;
	} else {
			echo '<!-- '.$result.' -->';
			echo '<script type="text/javascript">alert("'.$re->error_msg.'");</script>';
	}
}else{
  $tempcookie=$re->user->BDUSS;
  $temp2=explode('|', $tempcookie);
	$cookie='BDUSS='.$temp2[0];
	$return = bin2hex(authcode($cookie, 'ENCODE', $client['key']));
	echo $return;
	$url = "{$client[url]}api.php?action=receive_cookie&formhash={$formhash}";
	if(strexists($url, '.rhcloud.com')) $url = str_replace('http://', 'https://', $url);
	//header("Location: {$url}");
	echo '登陆成功，请稍候...<form method="post" action="'.$url.'" style="display: none" id="redirect"><input type="hidden" name="cookie" value="'.$return.'"><input type="submit"></form><script type="text/javascript">document.getElementById("redirect").submit();</script>';
	exit();
}

function fetch($url, $postdata = '', $cookie = ''){
	if(!$cookie) $cookie = get_cookie_string();
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_TIMEOUT, 6);
	curl_setopt($ch, CURLOPT_HEADER, true);
	curl_setopt($ch, CURLOPT_COOKIE, $cookie);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	if($postdata) curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
	$get_url = curl_exec($ch);
	if($get_url !== false){
		$statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		if ($statusCode >= 500) return false;
  	}
	prase_cookie($get_url);
    curl_close($ch);
    return $get_url;
}
function get_cookie_string(){
	global $CURL_COOKIE;
	$str = '';
	foreach ($CURL_COOKIE as $key => $val){
		if($key) $str .= "{$key}={$val}; ";
	}
	return $str;
}
function prase_cookie($header){
	global $CURL_COOKIE;
	if (preg_match_all('/Set-Cookie: ([^=]+)=([^;]+)/', $header, $match)){
		foreach ($match[1] as $key => $val){
			$k = trim($match[1][$key]);
			if(!$k) continue;
			if($k == 'BAEID') continue;
			$CURL_COOKIE[$k] = trim($match[2][$key]);
		}
	}
}
function prase_post_cookie(){
	global $CURL_COOKIE;
	if (preg_match_all('/([^=;]+)=([^;]+)/', $_POST['cookie'], $match)){
		foreach ($match[1] as $key => $val){
			$k = trim($match[1][$key]);
			if(!$k) continue;
			$CURL_COOKIE[$k] = trim($match[2][$key]);
		}
	}
}

list($domain) = explode('/', $client['host']);
?>
<!DOCTYPE html>
<html>
<head>
<title>账号绑定 - 贴吧签到助手</title>
<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
<meta name="HandheldFriendly" content="true" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
<meta name="author" content="kookxiang" />
<meta name="copyright" content="KK's Laboratory" />
<link rel="shortcut icon" href="/favicon.ico" />
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
<link rel="stylesheet" href="style/main.css" type="text/css" />
<link rel="stylesheet" href="api_login.css" type="text/css" />
</head>
<body>
<div class="wrapper" id="page_login">
<div class="center-box">
<h1>绑定账号</h1>
<form method="post" action="login.php?sid=<?php echo $_GET['sid']; ?>&parm=<?php echo $_GET['parm']; ?>" onsubmit="document.getElementById('submit').disabled=true">
<div class="login-info">
<p>合作网站： 贴吧签到助手 (<?php echo $domain; ?>)</p>
<p>百度通行证： <?php echo $username; ?></p>
<p>通行证密码： ******</p>
<?php
if($verifyAddress){
	$cookie = $cookie ? $cookie : get_cookie_string();
	echo <<<EOF
<p>验证码：
<img src="https://wappass.baidu.com/cgi-bin/genimage?{$verifyAddress}" class="verifycode" onclick="this.src=this.src+'&'" />
<input type="text" name="verifycode" placeholder="请输入验证码" autocomplete="off" required />
<input type="hidden" name="token" value="{$token}" />
<input type="hidden" name="cookie" value="{$cookie}" />
<input type="hidden" name="codestring" value="{$verifyAddress}" /></p>
EOF;
}
?>
<p><a href="manual_bind.php?sid=<?php echo $_GET['sid']; ?>&formhash=<?php echo $formhash; ?>">自动登录有问题？尝试手动绑定</a></p>
</div>
<p><input type="hidden" name="author" value="kookxiang" /></p>
<p class="btns">
<input type="submit" id="submit" value="绑定账号" />
<button onclick="window.close();">返回网站</button>
</p>
</form>
</div>
<p class="copyright">Designed by <a href="http://www.ikk.me" target="_blank">kookxiang</a> - <a href="http://go.ikk.me/donate" target="_blank">赞助开发</a><br>All right reserved, 2013 &copy; <a href="http://www.kookxiang.com" target="_blank">KK's Laboratory</a></p>
</div>
</body>
</html>