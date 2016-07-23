<?php
include 'system/common.inc.php';
if(!$_GET['sid']) throw new Exception('Illegal API request.');
if(in_array($_GET['sid'], $blacklist)) throw new Exception('Illegal API request.');
$formhash = $_GET['formhash'];
if(!$formhash) throw new Exception('Illegal package');
$sid = intval($_GET['sid']);
$client = DB::fetch_first("SELECT * FROM client WHERE id='{$sid}'");
if(!$client) throw new Exception('Illegal site.');
if($client['key'] == 'BLOCKED') showmessage('该网站在黑名单中，禁止使用此功能<br></p><ins>可能的原因：该网站非法盗取用户数据或篡改程序版权</ins><br><p style="display: none">', 'http://www.kookxiang.com', 7);
if(inBlackList($client['url'])) showmessage('该网站在黑名单中，禁止使用此功能<br></p><ins>可能的原因：该网站非法盗取用户数据或篡改程序版权或将本软件用于商业用途</ins><br><p style="display: none">', 'http://www.kookxiang.com', 7);
if(($cookie = $_GET['data']) || $_POST['cookie']){
	if($cookie){
		$cookie = base64_decode(rawurldecode($cookie));
	}else{
		$cookie = $_POST['cookie'];
	}
	if(!preg_match('/BDUSS=/i', $cookie)) showmessage('Cookie 中不含有 BDUSS 信息，请重新提交');
	$return = bin2hex(authcode($cookie, 'ENCODE', $client['key']));
	$url = "{$client[url]}api.php?action=receive_cookie&formhash={$formhash}";
	if(strexists($url, '.rhcloud.com')) $url = str_replace('http://', 'https://', $url);
	//header("Location: {$url}");
	echo '登陆成功，请稍候...<form method="post" action="'.$url.'" style="display: none" id="redirect"><input type="hidden" name="cookie" value="'.$return.'"><input type="submit"></form><script type="text/javascript">document.getElementById("redirect").submit();</script>';
	exit();
}
list($domain) = explode('/', $client['host']);
?>
<!DOCTYPE html>
<html>
<head>
<title>手动绑定 - 贴吧签到助手</title>
<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
<meta name="HandheldFriendly" content="true" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
<meta name="author" content="kookxiang" />
<meta name="copyright" content="KK's Laboratory" />
<link rel="shortcut icon" href="/favicon.ico" />
<link rel="chrome-webstore-item" href="https://chrome.google.com/webstore/detail/dpekabpinhnjcikegadjeopgmhphgnce">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
<link rel="stylesheet" href="style/main.css" type="text/css" />
<link rel="stylesheet" href="api_login.css" type="text/css" />
</head>
<body>
<div class="wrapper" id="page_login">
<div class="center-box">
<h1>手动绑定</h1>
<div class="login-info">
<p>合作网站： 贴吧签到助手</p>
<div id="tips">
<p style="color:red">注意： 手动绑定后，如果在浏览器中点击“退出”，会导致绑定失效！建议您在浏览器的“隐身窗口”中绑定账号。</p>
</div>
<p id="drag_txt">请拖动下方的链接到收藏夹中，然后点击：</p>
<p id="chr_txt" style="display:none;">检测到您的浏览器为Chrome内核，请安装以下插件<text style="color:red">（Chrome Stable\Beta不支持，请使用其它Chrome内核浏览器如Chrome DEV、360极速、Opera、猎豹等）</text></p>
<p id="box" class="box">
<a href="javascript:(function(){window.sid='<?php echo $sid; ?>';window.formhash='<?php echo $formhash; ?>';var c=document.createElement('script');c.type='text/javascript';c.src='//api.ikk.me/v2/bind.js';c.charset='utf-8';document.getElementsByTagName('head')[0].appendChild(c);})();" onclick="alert('请把我拖动到收藏夹中，\n然后点击收藏夹中的链接');return false;" id="btn">请把我拖动到收藏夹</a>
</p>
<form method="post" id="manual_input" action="manual_bind.php?sid=<?php echo $sid; ?>&formhash=<?php echo $formhash; ?>">
<p>如果你有通过其他途径获得的 Cookie 信息，请在此提交：<br>
<input type="text" id="cookie" name="cookie" placeholder="粘贴后按回车键提交" /></p>
</form>
<script type="text/javascript">
function chromeExtensionsCallback(){
	if(username == "undefined") return;
	document.getElementById('tips').innerHTML = '<p style="text-align: center"><a href="javascript:;" class="btn" onclick="chromeOneKeyLogin()">使用本机已经登陆的用户 '+username+'</a></p>';
	document.getElementById('box').style.display = 'none';
}
function chromeOneKeyLogin(){
	document.getElementById('manual_input').style.display = 'none';
	document.getElementById('cookie').value = cookie;
	document.getElementById('manual_input').submit();
}
if(typeof chrome == "object"){
	document.getElementById('drag_txt').style.display = 'none';
	document.getElementById('chr_txt').style.display = '';
	document.getElementById('btn').href = '/dl/GetCookie.crx';
	document.getElementById('btn').target = '_blank';
	document.getElementById('btn').onclick = function(){
		chrome.webstore.install('/dl/GetCookie.crx', function(){
			location.reload();
		});
		return false;
	};
	//document.getElementById('btn').onclick = null;
	document.getElementById('btn').innerHTML = '安装辅助绑定插件 For Chrome';
}
</script>
</div>
</div>
<p class="copyright">Designed by <a href="http://www.ikk.me" target="_blank">kookxiang</a> - <a href="https://me.alipay.com/kookxiang" target="_blank">赞助开发</a><br>All right reserved, 2014 &copy; <a href="http://www.kookxiang.com" target="_blank">KK's Laboratory</a></p>
</div>
</body>
</html>