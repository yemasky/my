<?php 
/*
	auther: cooc 
	email:yemasky@msn.com
*/

class PathManager {
	public function __construct() {
	}
	/*去web路径*/

	public static function getHtmlUrl($name, $arrValue = NULL) {
		$htmlurl = '';
		if(!empty($arrValue) && is_array($arrValue)) {
			foreach($arrValue as $k => $v) {
				if(is_array($v)) {
					foreach($v as $vk => $vv) {
						if(!empty($vv)) $htmlurl .= '--' . $vk . '-' . urlencode($vv); 
					}
				} else {
					if(!empty($v)) $htmlurl .= '--' . $k . '-' . urlencode($v); 
				}
			}
		}
		return $name . $htmlurl . '.html';
	}

	public static function getRegisterUrl() {
		return self::getHtmlUrl('register');
	}
	
	public static function getSiteUrl($channel, $pn = NULL, $videoId = NULL, $tagOrSeries = NULL) {
		if($channel == 'search') return __WEB . $channel . '.html?s=' . $videoId . '&pn=' . $pn;
		if($tagOrSeries != '' && $videoId == NULL) $tagOrSeries = $tagOrSeries . '-';
		if($pn) return __WEB . $channel . '/' . $tagOrSeries . $pn . '.html';
		if($tagOrSeries > 0 && $videoId > 0) $tagOrSeries = '-' . $tagOrSeries;
		if($videoId > 0) return __WEB . $channel . '/view/' . $videoId . $tagOrSeries . '.html';
		return __WEB . $channel . '/';
	}
	
	public static function getPlayUrl($channel, $videoId, $series) {
		return __WEB . $channel . '/video/' . $videoId . '-' . $series . '.html';
	}
	
	public static function getPageArray($allpage, $pn, $pass_pn = 5, $pn_length = 10) {
		$arrPages = NULL;
		$i = 0;
		$k = 0;
		$pass = 0;
		if($pn > 1) {
			$arrPages[$i]['title'] = '&lsaquo;';
			$arrPages[$i]['pn'] = $pn - 1;
			$arrPages[$i]['url'] = $pn - 1;
			$i++;
		}
		if($allpage < $pn_length) {
			if($pn > 1) {
				for($i; $i <= $allpage; $i++) {
					$arrPages[$i]['title'] = $i;
					$arrPages[$i]['pn'] = $i;
				}
			} else {
				for($i; $i < $allpage; $i++) {
					$arrPages[$i]['title'] = $i + 1;
					$arrPages[$i]['pn'] = $i + 1;;
				}
			}
			$i--;
		} else {
			if($pn <= $pass_pn) {
				for($i; $i < $pn_length; $i++) {
					$arrPages[$i]['title'] = $i + 1;
					$arrPages[$i]['pn'] = $i + 1;
				}
			} else {
				$pass = $pn - $pass_pn - 1;
				if(($allpage - $pass) < $pn_length) $pass = $allpage - $pn_length;
				$arrPages[$i]['title'] = '1';
				$arrPages[$i]['pn'] = 1;
				$i++;
				$arrPages[$i]['title'] = '...';
				$arrPages[$i]['pn'] = '#pn';
				$i++;
				for($k; $k < $pn_length; $k++) {
					$arrPages[$i + $k]['title'] = $i + $k + $pass;
					$arrPages[$i + $k]['pn'] = $i + $k + $pass;
					if(($i + $k + $pass) == $allpage) break;
				}
				
			}
			if(($i + $k + $pass) == $allpage) {
				$arrPages[$i + $k]['title'] = $allpage;
				$arrPages[$i + $k]['pn'] = $allpage;
				$i = $i + $k;
			} else {
				$arrPages[$i + $k]['title'] = '...';
				$arrPages[$i + $k]['pn'] = '#pn';
				$arrPages[$i + $k + 1]['title'] = $allpage;
				$arrPages[$i + $k + 1]['pn'] = $allpage;
				$i = $i + $k + 1;
			}
		}
		if($pn < $allpage) {
			$arrPages[$i + 1]['title'] = '&rsaquo;';
			$arrPages[$i + 1]['pn'] = $pn + 1;
		}
		return $arrPages;
				
	}
	

	/*取物理路径*/
	public static function createCacheDir($cacheid, $dir = __CACHE, $renew_cachedir = true) {
		if(!is_dir($dir)) File::createDir($dir);
		if($renew_cachedir) {
			$cacheid = self::creatMd5FileId($cacheid);
			$dir = $dir . $cacheid . '/';
			File::createDir($dir);
		}
		return $dir;
	}
	
	public static function getCacheDir($cacheid, $dir = __CACHE, $renew_cachedir = true) {
		if($renew_cachedir) return  $dir . self::creatMd5FileId($cacheid) . '/';
		return  $dir;
	}
	
	//取得用户缓存文件夹
	public static function getUserDir($uid) {
		return __USER_DATA . self::creatNumFileId($uid) . '/' . $uid . '/';
	}
	
	//创建用户缓存文件夹
	public static function createUserDir($uid) {
		$fileid = self::creatNumFileId($uid);
		File::createDir(__USER_DATA.'/'.$fileid);
		File::createDir(__USER_DATA.'/'.$fileid.'/'.$uid);
		return __USER_DATA . $fileid.'/'.$uid . '/';
	}
	
	//取得用户图像文件夹
	public static function getUserImgDir($uid) {
		return __USER_IMG . self::creatNumFileId($uid) . '/' . $uid . '/';
	}
	
	//创建用户图像文件夹
	public static function createUserImgDir($uid) {
		$fileid = self::creatNumFileId($uid);
		File::createDir(__USER_IMG.'/'.$fileid);
		File::createDir(__USER_IMG.'/'.$fileid.'/'.$uid);
		return __USER_IMG . $fileid.'/'.$uid . '/';
	}
	//用户图像文件夹web路径
	public static function getUserImgWeb($uid) {
		return __USER_IMGWEB . self::creatNumFileId($uid) . '/' . $uid . '/';
	}
	
	//创建数据文件夹 返回日期形式的路径 
	public static function createDataDir($uid, $type = 'img') {
		$fileid = self::creatNumFileId($uid);
		$d = date('Y');
		$m = date('m') . '-' . date('d');
		File::createDir(__DATA.$d);
		File::createDir(__DATA.$d.'/'.$m);
		File::createDir(__DATA.$d.'/'.$m.'/'.$fileid);
		File::createDir(__DATA.$d.'/'.$m.'/'.$fileid.'/'.$uid);
		File::createDir(__DATA.$d.'/'.$m.'/'.$fileid.'/'.$uid.'/'.$type);
		return $d.'/'.$m.'/'.$fileid.'/'.$uid.'/'.$type.'/';
	}
	
	//取得数字文件ID
	public static function creatNumFileId($id, $len = 3, $max = 0, $ext = NULL) {
		if($max > 0) $id = $id % $max;
		$id_len = strlen($id);
		if($id_len >= $len) {
			$fileid = substr($id, -$len);
		} else {
			$i_len = '000000000000000000';
			$fileid = substr($i_len,$id_len - $len) . $id;
		} 
		return $ext . $fileid;
	}
	
	////取得md5文件ID
	public static function creatMd5FileId($md5id, $len = -3) {
		return substr($md5id, $len);
	}

}
?>