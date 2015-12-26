<?php
/*
	class.Searchkw.php
	auther: cooc 
	email:yemasky@msn.com
*/
class Searchkw {
	public static function kw($mydata, $turn = true) {
		if(empty($mydata)) return $mydata;
		if($turn) $mydata = iconv('UTF-8', 'GBK', $mydata);
		
		$dict = __ROOT_PATH . '/libs/pscws/dict/dict.xdb';	// 默认采用 xdb (不需其它任何依赖)
		//$mydata  = NULL;	// 待切数据
		$version = 3;		// 采用版本
		$autodis = true;	// 是否识别名字
		$ignore  = true;	// 是否忽略标点
		$debug   = false;	// 是否为除错模式
		$stats	 = false;	// 是否查看统计结果
		//$is_cli  = (php_sapi_name() == 'cli');	// 是否为 cli 运行环境
		$object = 'PSCWS' . $version;
		require_once (__ROOT_PATH . '/libs/pscws/pscws3.class.php');
			
		$cws = new PSCWS3($dict);
		$cws->set_ignore_mark($ignore);
		$cws->set_autodis($autodis);
		$cws->set_debug($debug);
		// hightman.060330: 强行开启统计
		$cws->set_statistics($stats);
		
		// 执行切分, 分词结果数组执行 words_cb()
		$result = $cws->segment($mydata);
		$str = '';
		if(is_array($result) && !empty($result)) {
			foreach ($result as $k => $v) {
				$str .= $v . ' ';
			}
		}
		if($turn) $str = iconv('GBK', 'UTF-8', $str);
		return trim($str);
	}
}
?>