<?php
/*
 * func.Common.php
 * auther: cooc
 * email:yemasky@msn.com
 */
if(!defined("INC_FUNC_COMMON")) {
	define("INC_FUNC_COMMON", "1");
	
	define("__MODEL_EMPTY", "");
	define("__MODEL_EXCEPTION", "Exception");
	
	date_default_timezone_set('PRC');
	if(isset($_SERVER['argc']) && $_SERVER['argc'] >= 0) {
		$arrVariables = $_SERVER['argv'];
		$_REQUEST = NULL;
		if(!empty($arrVariables[0])) {
			foreach($arrVariables as $k => $v) {
				$arrVariable = explode('=', $v);
				if(!isset($arrVariable[1]))
					$arrVariable[1] = NULL;
					$arrParameter[$arrVariable[0]] = $arrVariable[1];
			}
		}
		$_REQUEST = $arrParameter;
	}
	//function __autoload($class){
	function process_autoload(string $class){
		$len = strlen($class) - 1;
		for($loop = $len; $loop >= 0; $loop--) {
			if($class[$loop] >= 'A' && $class[$loop] <= 'Z') {
				break;
			}
		}
		$execute_type = substr($class, $loop);
		$execute_dir = 'base/';
		$pos = strpos($class, '\\');
		if($pos != false) {
			$execute_dir = substr($class, 0, $pos) . '/';
			$class =  substr($class, $pos);
		}
		
		switch($execute_type){
			case "Action" :
				$execute_dir = "process/" . $execute_dir . "action/";
				break;
			case "Dao" :
				$execute_dir = "process/" . $execute_dir . "dao/";
				break;
			case "Common" :
				$execute_dir = "process/" . $execute_dir . "common/";
				break;
			case "Service" :
				$execute_dir = "process/" . $execute_dir . "service/";
				break;
			case "Util" :
				$execute_dir = "process/" . $execute_dir . "utils/";
				break;
			case "Tool" :
				$execute_dir = "process/" . $execute_dir . "tool/";
				break;
			case "Config" :
				$execute_dir = "process/" . $execute_dir . "config/";
				break;
			default :
				$execute_dir = 'libs/';
				break;
		}
		$classes_file = __ROOT_PATH . $execute_dir . $class . ".class.php";
		if(file_exists($classes_file)) {
			include_once ($classes_file);
		} else {
			//throw new Exception("unable to load class: $class , patch->". $classes_file);
		}
	}
	spl_autoload_register("process_autoload");

	function getDateTime(int $d = 0):string {
		return date("Y-m-d H:i:s", strtotime("$d HOUR")); // GMT+8
	}

	function getHis():string {
		return date("His");
	}

	function getDateTimeId(int $l = 6, int $d = 8):string {
		$time = date("YmdHis", strtotime("+$d HOUR")); // GMT+8
		$time .= trim(substr(microtime(), 2, $l));
		return $time;
	}

	function logError(string $message, string $model = "", string $level = "ERROR"){
		writeLog("#error", $message);
	}

	function logDebug(string $message, string $model = "", string $level = "DEBUG"){
		writeLog("#debug", $message);
	}

	function writeLog(string $filename, string $msg){
		if(defined('__WWW_LOGS_PATH')) {
			$fp = fopen(__WWW_LOGS_PATH . $filename . '.' . date("z") . '.log', "a+");
		} else {
			$fp = fopen(__ROOT_LOGS_PATH . $filename . '.' . date("z") . '.log', "a+");
		}
		$uri = '';
		if(isset($_SERVER['REQUEST_URI']))
			$uri = $_SERVER['REQUEST_URI'];
		$msg = getDateTime() . " >>> " . $uri . ' >> ' . $msg;
		fwrite($fp, "$msg\r\n");
		fclose($fp);
	}

	function redirect(string $url, string $status = '302', int $time = 0){
		if(is_numeric($url)) {
			header("Content-type: text/html; charset=" . __CHARSET);
			echo "<script>history.go('$url')</script>";
			flush();
		} else {
			if($time > 0) {
				echo "<meta http-equiv=refresh content=\"$time; url=$url\">";
				exit();
			}
			if(headers_sent()) {
				echo "<meta http-equiv=refresh content=\"0; url=$url\">";
				echo "<script type='text/javascript'>location.href='$url';</script>";
			} else {
				if($status == '302') {
					header("HTTP/1.1 302 Moved Temporarily");
					header("Location: $url");
					exit();
				}
				header("Cache-Control: no-cache, must-revalidate");
				header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
				header("HTTP/1.1 301 Moved Permanently");
				header("Location: $url");
			}
		}
		exit();
	}

	function errorLog(string $msg){
		logError("[err]$msg;request ip:" . onLineIp() . ";url:" . getUrl() . ";ReferUrl:" . getReferUrl() . ";time:" . getDateTime());
		// throw new Exception("[err]$msg;request ip:".onLineIp().";url:".getUrl().";ReferUrl:".getReferUrl().";time:".getDateTime());
	}

	function getMicrotime():string{
		list($usec, $sec) = explode(" ", microtime());
		return ((float)$usec + (float)$sec);
	}

	function onLineIp():string{
		if(isset($_SERVER['HTTP_CLIENT_IP'])) {
			$onlineip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif(isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$onlineip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
			$onlineip = $_SERVER['REMOTE_ADDR'];
		}
		return $onlineip;
	}

	function getHost():string{
		return $_SERVER['HTTP_HOST'];
	}

	function getUrl():string{
		if($_SERVER["SERVER_PORT"] == 80) {
			return 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		}
		return 'http://' . $_SERVER['HTTP_HOST'] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER['REQUEST_URI'];
	}

	function getReferUrl():string{
		if(!isset($_SERVER['HTTP_REFERER']))
			return NULL;
		if(($url = $_SERVER['HTTP_REFERER']) == NULL) {
			return '/';
		}
		return $url;
	}

	function conutf8(string $v, string $e = 'GBK', string $c = 'UTF-8'):string{
		return iconv($e, $c, $v);
	}

	function alert(string $mess, int $go = -1):string{
		if(!headers_sent()) {
			header("Content-type: text/html; charset=" . __CHARSET);
		}
		$script = $go == 0 ? "" : "history.go(" . $go . ");";
		echo "<script>alert('" . $mess . "');$script</script>";
		flush();
		if(!empty($script)) {
			exit();
		}
	}

	function ErrorHandler(string $errno, string $errstr, string $errfile, int $errline){
		if(!(error_reporting() & $errno)) { // This error code is not included in error_reporting
			return;
		}
		$msg = '';
		switch($errno){
			case E_USER_ERROR :
				$msg .= "ERROR [$errno] $errstr ;";
				$msg .= "  Fatal error on line $errline in file $errfile";
				$msg .= ", PHP " . PHP_VERSION . " (" . PHP_OS . ") ;";
				// $msg .= "Aborting..;";
				// exit(1);
				break;
			
			case E_USER_WARNING :
				$msg .= "WARNING [$errno] $errstr on line $errline in file $errfile;";
				break;
			
			case E_USER_NOTICE :
				$msg .= "NOTICE [$errno] $errstr on line $errline in file $errfile;";
				break;
			
			default :
				$msg .= "Unknown error type: [$errno] $errstr on line $errline in file $errfile;";
				break;
		}
		if(__Debug) {
			print_r($msg);
		}
		writeLog('errorHandler.' . date("z") . '.log', $msg);
		/* Don't execute PHP internal error handler */
		return true;
	}

	function cutString(string $str, int $len, int $start = 0):string{
		if(strlen($str) <= $len) {
			return $str;
		}
		for($loop = 0; $loop < $len; $loop++) {
			if(ord($str[$loop]) > 224) {
				$loop += 2;
				continue;
			}
			if(ord($str[$loop]) > 192) {
				$loop++;
			}
		}
		/*
		 * if($loop == $len + 1) {
		 * $len--;
		 * }
		 */
		return substr($str, 0, $loop);
	}

	function page(int $pn, int $all_page_num, int $show_pages = 10) {
		$arrayResultPage = [];
		$mod_pn = $pn % $show_pages;
		if($mod_pn != 0) {
		} else {
			$mod_pn = $show_pages;
		}
		if($pn > $show_pages) {
			$arrayResultPage[0] = $pn - $mod_pn - $show_pages + 1;
		} else {
			$arrayResultPage[0] = '';
		}
		if(($all_page_num -  $pn) < $show_pages) {
			if($mod_pn <= ($all_page_num % $show_pages)) {
				$show_pages = $all_page_num % $show_pages;
			}
		}
		for($i = 1; $i <= $show_pages; $i++) {
			$arrayResultPage[$i] = $pn - $mod_pn + $i;
		}
		if($pn < ($all_page_num - $show_pages)) {
			$arrayResultPage[$i] = $arrayResultPage[$i -1] + 1;
		} else {
			$arrayResultPage[$i] = '';
		}
		return $arrayResultPage;
	}
}
?>
