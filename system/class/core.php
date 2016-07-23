<?php
if(!defined('IN_KKFRAME')) exit();
class db_mysql{
	var $curlink;
	var $last_query;
	function connect(){
		global $_config;
		$this -> curlink = $this -> _dbconnect($_config['db']['server'] . ':' . $_config['db']['port'], $_config['db']['username'], $_config['db']['password'], 'utf8', $_config['db']['name'], false);
	}
	function _dbconnect($dbhost, $dbuser, $dbpw, $dbcharset, $dbname, $pconnect){
		$link = null;
		$func = empty($pconnect) ? 'mysql_connect' : 'mysql_pconnect';
		if(!$link = @$func($dbhost, $dbuser, $dbpw, 1)){
			$this -> halt('Couldn\'t connect to MySQL Server');
		}else{
			$this -> curlink = $link;
			if($this -> version() > '4.1'){
				$serverset = $dbcharset ? 'character_set_connection=' . $dbcharset . ', character_set_results=' . $dbcharset . ', character_set_client=binary' : '';
				$serverset .= $this -> version() > '5.0.1' ? ((empty($serverset) ? '' : ',') . 'sql_mode=\'\'') : '';
				$serverset && mysql_query("SET $serverset", $link);
			}
			$dbname && @mysql_select_db($dbname, $link);
		}
		return $link;
	}
	function select_db($dbname){
		return mysql_select_db($dbname, $this -> curlink);
	}
	function fetch_array($query, $result_type = MYSQL_ASSOC){
		return mysql_fetch_array($query, $result_type);
	}
	function fetch_first($sql){
		return $this -> fetch_array($this -> query($sql));
	}
	function result_first($sql){
		return $this -> result($this -> query($sql), 0);
	}
	function query($sql, $type = ''){
		$func = $type == 'UNBUFFERED' && @function_exists('mysql_unbuffered_query') ? 'mysql_unbuffered_query' : 'mysql_query';
		if(!$this -> curlink) $this -> connect();
		if(!($query = $func($sql, $this -> curlink))){
			if($type != 'SILENT'){
				$this -> halt('MySQL Query ERROR', $sql);
			}
		}
		DEBUG :: query_counter();
		return $this -> last_query = $query;
	}
	function affected_rows(){
		return mysql_affected_rows($this -> curlink);
	}
	function error(){
		return (($this -> curlink) ? mysql_error($this -> curlink) : mysql_error());
	}
	function errno(){
		return intval(($this -> curlink) ? mysql_errno($this -> curlink) : mysql_errno());
	}
	function result($query, $row = 0){
		$query = @mysql_result($query, $row);
		return $query;
	}
	function num_rows($query){
		$query = mysql_num_rows($query);
		return $query;
	}
	function num_fields($query){
		return mysql_num_fields($query);
	}
	function free_result($query){
		return mysql_free_result($query);
	}
	function insert_id(){
		return ($id = mysql_insert_id($this -> curlink)) >= 0 ? $id : $this -> result($this -> query("SELECT last_insert_id()"), 0);
	}
	function fetch_row($query){
		$query = mysql_fetch_row($query);
		return $query;
	}
	function fetch_fields($query){
		return mysql_fetch_field($query);
	}
	function version(){
		if(empty($this -> version)){
			$this -> version = mysql_get_server_info($this -> curlink);
		}
		return $this -> version;
	}
	function close(){
		return mysql_close($this -> curlink);
	}
	function halt($message = '', $sql = ''){
		error :: db_error($message, $sql);
	}
	function __destruct(){
		$this -> close();
	}
}
class DB{
	function delete($table, $condition, $limit = 0, $unbuffered = true){
		if(empty($condition)){
			$where = '1';
		}elseif(is_array($condition)){
			$where = DB :: implode_field_value($condition, ' AND ');
		}else{
			$where = $condition;
		}
		$sql = "DELETE FROM {$table} WHERE $where " . ($limit ? "LIMIT $limit" : '');
		return DB :: query($sql, ($unbuffered ? 'UNBUFFERED' : ''));
	}
	function insert($table, $data, $return_insert_id = true, $replace = false, $silent = false){
		$sql = DB :: implode_field_value($data);
		$cmd = $replace ? 'REPLACE INTO' : 'INSERT INTO';
		$silent = $silent ? 'SILENT' : '';
		$return = DB :: query("$cmd $table SET $sql", $silent);
		return $return_insert_id ? DB :: insert_id() : $return;
	}
	function update($table, $data, $condition, $unbuffered = false, $low_priority = false){
		$sql = DB :: implode_field_value($data);
		$cmd = "UPDATE " . ($low_priority ? 'LOW_PRIORITY' : '');
		$where = '';
		if(empty($condition)){
			$where = '1';
		}elseif(is_array($condition)){
			$where = DB :: implode_field_value($condition, ' AND ');
		}else{
			$where = $condition;
		}
		$res = DB :: query("$cmd $table SET $sql WHERE $where", $unbuffered ? 'UNBUFFERED' : '');
		return $res;
	}
	function implode_field_value($array, $glue = ','){
		$sql = $comma = '';
		foreach ($array as $k => $v){
			$sql .= $comma . "`$k`='$v'";
			$comma = $glue;
		}
		return $sql;
	}
	function insert_id(){
		return DB :: _execute('insert_id');
	}
	function fetch($resourceid, $type = MYSQL_ASSOC){
		return DB :: _execute('fetch_array', $resourceid, $type);
	}
	function fetch_first($sql){
		return DB :: _execute('fetch_first', $sql);
	}
	function fetch_all($sql){
		$query = DB :: _execute('query', $sql);
		$return = array();
		while($result = DB :: fetch($query)){
			$return[] = $result;
		}
		return $return;
	}
	function result($resourceid, $row = 0){
		return DB :: _execute('result', $resourceid, $row);
	}
	function result_first($sql){
		return DB :: _execute('result_first', $sql);
	}
	function query($sql, $type = ''){
		return DB :: _execute('query', $sql, $type);
	}
	function num_rows($resourceid){
		return DB :: _execute('num_rows', $resourceid);
	}
	function affected_rows(){
		return DB :: _execute('affected_rows');
	}
	function free_result($query){
		return DB :: _execute('free_result', $query);
	}
	function error(){
		return DB :: _execute('error');
	}
	function errno(){
		return DB :: _execute('errno');
	}
	function _execute($cmd , $arg1 = '', $arg2 = ''){
		static $db;
		if(empty($db)) $db = & DB :: object();
		$res = $db -> $cmd($arg1, $arg2);
		return $res;
	}
	function & object(){
		static $db;
		if(empty($db)) $db = new db_mysql();
		return $db;
	}
}
class DEBUG{
	function INIT(){
		$GLOBALS['debug']['time_start'] = self :: getmicrotime();
		$GLOBALS['debug']['query_num'] = 0;
	}
	function getmicrotime(){
		list($usec, $sec) = explode(' ', microtime());
		return ((float)$usec + (float)$sec);
	}
	function output(){
		$return[] = 'MySQL 请求 ' . $GLOBALS['debug']['query_num'] . ' 次';
		$return[] = '运行时间：' . number_format((self :: getmicrotime() - $GLOBALS['debug']['time_start']), 6) . '秒';
		return implode(' , ', $return);
	}
	function query_counter(){
		$GLOBALS['debug']['query_num']++;
	}
	function MSG($string){
		if($_GET['debug']) echo "{$string}\r\n";
	}
}

class mail_content{
	var $address;
	var $subject;
	var $message;
}
class mailsender{
	var $obj;
	function sendMail($mail){
		return false;
	}
}
class kk_sign{
	var $m = array('cache', 'error');
	function kk_sign($n = array()){
		global $_config;
		require_once SYSTEM_ROOT . './config.cfg.php';
		foreach($this -> m as $m){
			require_once SYSTEM_ROOT . "./class/{$m}.php";
		}
		DEBUG :: INIT();
		require_once SYSTEM_ROOT . './function/core.php';
		$this -> a();
		$this -> b();
		foreach($n as $m){
			$mm = "_load_module_{$m}";
			if(method_exists($this, $mm)){
				$this -> $mm();
			}else{
				$this -> _load_module($m);
			}
		}
	}
	function __destruct(){
		if(!defined('SYSTEM_STARTED')) return;
		flush();
		ob_end_flush();
	}
	function a(){
		ob_start();
		header('Content-type: text/html; charset=utf-8');
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		header('Cache-Control: no-cache');
		header('Pragma: no-cache');
		@date_default_timezone_set('Asia/Shanghai');
	}
	function b(){
		$ua = strtolower($_SERVER['HTTP_USER_AGENT']);
		if(strpos($ua, 'wap') || strpos($ua, 'mobi') || strpos($ua, 'opera') || $_GET['mobile']){
			define('IN_MOBILE', true);
		}else{
			define('IN_MOBILE', false);
		}
		if(strpos($ua, 'bot') || strpos($ua, 'spider')) define('IN_ROBOT', true);
	}
	function c(){
		if(SYS_KEY){
			define('ENCRYPT_KEY', SYS_KEY);
		}elseif(!getSetting('SYS_KEY')){
			$key = random(32);
			saveSetting('SYS_KEY', $key);
			define('ENCRYPT_KEY', $key);
		}else{
			define('ENCRYPT_KEY', getSetting('SYS_KEY'));
		}
	}
	function d(){
		global $cookiever, $uid, $username;
		$cookiever = '2';
		if(!empty($_COOKIE['token'])){
			list($cc, $uid, $username, $l, $p) = explode("\t", authcode($_COOKIE['token'], 'DECODE'));
			if(!$uid || $cc != $cookiever){
				unset($uid, $username, $l);
				dsetcookie('token');
			}elseif($l < TIMESTAMP){
				$u = DB :: fetch_first("SELECT * FROM member WHERE uid='{$uid}'");
				$pp = substr(md5($u['password']), 8, 8);
				if($u && $p == $pp){
					$l = TIMESTAMP + 900;
					dsetcookie('token', authcode("{$cookiever}\t{$uid}\t{$u[username]}\t{$l}\t{$p}", 'ENCODE'));
				}else{
					unset($uid, $username, $l);
					dsetcookie('token');
				}
			}
		}else{
			$uid = $username = '';
		}
	}
	function _load_module($classname){
		require_once SYSTEM_ROOT . "./class/{$classname}.php";
	}
}
