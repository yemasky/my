<?php
/*
	class.GetContent.php
	auther: cooc 
	email:yemasky@msn.com
*/

class GetContent {
	private static $userAgent = '';
	private static $arrAgent = NULL;
	private static $arrAgentCookie = NULL;
	private static $headerCookie = false;
		/* get content */
	public static function getCurl($servleturl, $name = NULL, $pass = NULL, $postdata = NULL, $referer = false, $ssl = false)  {
		set_time_limit(0);
		$cUrl = curl_init();
		$referer = $referer == false ? NULL : $referer;
		if($ssl == false) {
			curl_setopt($cUrl, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($cUrl, CURLOPT_SSL_VERIFYHOST, FALSE);
		} else {
			curl_setopt($cUrl,CURLOPT_SSL_VERIFYPEER,true); ;
			curl_setopt($cUrl,CURLOPT_CAINFO, $ssl);
		}	
		curl_setopt($cUrl, CURLOPT_URL, $servleturl);
		curl_setopt($cUrl, CURLOPT_REFERER, $referer); // 
		curl_setopt($cUrl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
		curl_setopt($cUrl, CURLOPT_RETURNTRANSFER, 1);
		if($name != NULL && $pass != NULL) {
			$cookie_jar = __CRAWL . md5($name)."cookie.txt";
			curl_setopt($cUrl, CURLOPT_COOKIEJAR, $cookie_jar);
			curl_setopt($cUrl, CURLOPT_COOKIEFILE, $cookie_jar);
			//curl_setopt($cUrl, CURLOPT_COOKIE, self::cookieToStr($_COOKIE)); 
		}
		if($postdata != NULL) {
			curl_setopt($cUrl, CURLOPT_POST, 1); 
			curl_setopt($cUrl, CURLOPT_POSTFIELDS, $postdata);
		}
		curl_setopt($cUrl, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($cUrl, CURLOPT_TIMEOUT, 30);
		//curl_setopt($cUrl, CURLOPT_USERAGENT, "Connection: Keep-Alive");
		
		$pageContent = trim(curl_exec($cUrl));
		curl_close($cUrl);
		return $pageContent;
	}

	public static function simpleGetCurl($servleturl, $name = NULL, $cookie = true, $referer = false, $rand = true)  {
		$cUrl = curl_init();
		$referer = $referer == false ? $servleturl : $referer;
		curl_setopt($cUrl, CURLOPT_URL, $servleturl);
		curl_setopt($cUrl, CURLOPT_REFERER, $referer); // 
		curl_setopt($cUrl, CURLOPT_USERAGENT, self::setUserAgent($rand));
		curl_setopt($cUrl, CURLOPT_RETURNTRANSFER, 1);// 获取的信息以文件流的形式返回
		if(!empty($cookie)) {
			curl_setopt($cUrl, CURLOPT_HEADER, 0);//设定是否输出页面内容 
			curl_setopt($cUrl, CURLOPT_FOLLOWLOCATION, 1);// 不使用自动跳转
			//if(!empty($name)) {
				//if($rand) $name = md5(self::$userAgent).$name;
				$cookie_jar = __CRAWL . md5($name)."cookie.txt";
				curl_setopt($cUrl, CURLOPT_COOKIEJAR, $cookie_jar);
				curl_setopt($cUrl, CURLOPT_COOKIEFILE, $cookie_jar);
			//} else {
				if($cookie !== true) {
					curl_setopt($cUrl, CURLOPT_COOKIE, $cookie); 
				} else {
					curl_setopt($cUrl, CURLOPT_COOKIE, self::cookieToStr($rand)); 
				}
			//}
		}
		curl_setopt($cUrl, CURLOPT_TIMEOUT, 30);
		curl_setopt($cUrl, CURLOPT_ENCODING, "gzip" );
		curl_setopt($cUrl, CURLOPT_HTTPHEADER, array(
                "User-Agent:	".self::setUserAgent($rand),
                "Accept-Language:	zh-cn,zh;q=0.8,en-us;q=0.5,en;q=0.3",
				"Accept:	text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8",
				"Accept-Encoding:	gzip, deflate",
				"Connection:	keep-alive"
            ));

		
		$pageContent = curl_exec($cUrl);
		$info = curl_getinfo($cUrl);
		$strHead = substr($pageContent,0,$info['header_size']);
		preg_match("/Set-Cookie:(.+?)\n/i", $strHead, $arrCookie);
		if(!empty($arrCookie[1])) {
			self::$arrAgentCookie[md5(self::$userAgent)] = $arrCookie[1];
			self::$headerCookie = true;
			//echo $arrCookie[1] . "<br>";
		}
		curl_close($cUrl);//echo substr($pageContent,$info['header_size']+1);
		//if(!empty($cookie) && !empty($name)) 
		return $pageContent;
	}

	public static function cookieToStr($rand = true) {
		$userAgent = self::$userAgent;
		$md5id = md5($userAgent);
		$strCookie = '';
		if(self::$headerCookie) {
			if(isset(self::$arrAgentCookie[$md5id])) $strCookie = self::$arrAgentCookie[$md5id];
		} else {
			if(!empty($_COOKIE) && count($_COOKIE) > 0) {
				foreach($_COOKIE as $k => $v) {
					$strCookie .= $k . '=' . $v . ';';
				}
				foreach($_COOKIE as $k => $v) {
					//unset($_COOKIE[$k]);
				}
			}
			if(isset(self::$arrAgentCookie[$md5id])) {
				$strCookie = self::$arrAgentCookie[$md5id];
			} else {
				self::$arrAgentCookie[$md5id] = $strCookie;
			}
		}
		writeLog('#crawQiyi.tv.cookie.log',$strCookie . '<>' . $userAgent);
		return $strCookie;
	}
	
	public static function setUserAgent($rand = true) {
		if(!$rand) return $_SERVER['HTTP_USER_AGENT'];
		$arrUserAgent = self::$arrAgent;
		if(empty($arrUserAgent)) {
			include(__ROOT_PATH . 'etc/crawlConfig.php');
			self::$arrAgent = $arrUserAgent;
		}
		$num = count($arrUserAgent) - 1;
		if(self::$userAgent == '') {
			self::$userAgent = $arrUserAgent[$num];
			return self::$userAgent;
		}
		$num = rand(0, $num);
		$userAgent = $arrUserAgent[$num];
		self::$userAgent = $userAgent;
		return $userAgent;
	}
	
	public static function request_headers() {
		if(function_exists("apache_request_headers")) {
			if($headers = apache_request_headers()) {
				return $headers;
			}
		}
		$headers = array();
		foreach(array_keys($_SERVER) as $skey){ 
			if(substr($skey, 0, 5) == "HTTP_") {
				$headername = str_replace(" ", "-", ucwords(strtolower(str_replace("_", " ", substr($skey, 0, 5)))));
				$headers[$headername] = $_SERVER[$skey];
			}
		}
		return $headers;
	} 
	
	public static function pregmatchContent($preg, $content)  {
		preg_match($preg, $content, $arrMatches);
		return $arrMatches;
	}
	
	public static function pregmatchallContent($preg, $content)  {
		preg_match_all($preg, $content, $arrMatches);
		return $arrMatches;
	}
	
	public static function getSocket($host, $target = '/', $byte = 1024, $port = 80) {
		$fp = fsockopen($host, $port, $errno, $errstr, 30);
		if (!$fp) {
			//echo "$errstr ($errno)<br />\n";
			return "$errstr ($errno)<br />\n";
		} else {
			$out = "GET $target HTTP/1.1\r\n";
			$out .= "Host: $host\r\n";
			$out .= "Connection: Close\r\n\r\n";
			fwrite($fp, $out);
			$str = '';
			while (!feof($fp)) {
				$str .= fgets($fp, $byte);
			}
			fclose($fp);
			return $str;
		}	
	}
	
	public static function getByte($filename, $byte = NULL) {
		$handle = fopen($filename, "r");
		if(!$handle) return NULL;
		$contents = '';
		if(empty($byte)) $byte = filesize($filename);
		while (!feof($handle)) {
			$contents .= fread($handle, 8192);
			if(strlen($contents) >= $byte) break;
		}
		fclose($handle);	
		return $contents;
	}
}
?>