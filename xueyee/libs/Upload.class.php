<?php 
/*
	auther: cooc 
	email:yemasky@msn.com
*/
 
 class Upload {
	private static $arrMineType = array('application/octet-stream' => '.gif,.png,.jpg,.rar,.zip,.doc,.tgz,.mpeg,.mp3,.mov,.ppt,.pdf,.js,.chm,.3gp',
										'application/msword' => '.doc','image/gif' => '.gif','application/mshelp' => '.chm',
										'text/html' => '.html','text/javascript' => '.js',
										'image/jpeg' => '.jpg','video/mpeg' => '.mpeg',
										'image/pjpeg' => '.jpg',
										'image/png' => '.png','application/x-compressed-tar' => '.tgz',
										'audio/mpeg' => '.mp3','video/quicktime' => '.mov',
										'application/msaccess' => '.mdb','application/mspowerpoint' => '.ppt',
										'application/pdf' => '.pdf','application/vnd.rn-realmedia' => '.rmvb',
										'application/x-shockwave-flash' => '.swf','text/plain' => '.txt',
										'application/x-zip-compressed' => '.zip','video/3gpp' => '.3gp'
										);
							  
 	public static function UploadFlieArray($uploadfile) {
		$arrUpload = array();
		$i = 0;
 		foreach($_FILES[$uploadfile]['error'] as $k => $v) {
			if($v == '0' && is_uploaded_file($_FILES[$uploadfile]['tmp_name'][$k])) {
				$arrUpload[$i]['name'] = $_FILES[$uploadfile]['name'][$k];
				$arrUpload[$i]['size'] = $_FILES[$uploadfile]['size'][$k];
				$arrUpload[$i]['path'] = $_FILES[$uploadfile]['tmp_name'][$k];
				$arrUpload[$i]['type'] = self::checkUploadFileType($arrUpload[$i]['name'], self::$arrMineType[$_FILES[$uploadfile]['type'][$k]]);
			} else {
				$arrUpload[$i]['path'] = false;
			}
			$i++;
		}
		return $arrUpload;
 	}
	
	public static function UploadFlieSingle($uploadfile) {
		$arrUpload = array();
		$arrUpload['path'] = false;
		if(isset($_FILES[$uploadfile])) {
			if(is_uploaded_file($_FILES[$uploadfile]['tmp_name']) && $_FILES[$uploadfile]['error'] == '0')	{
				$arrUpload['name'] = $_FILES[$uploadfile]['name'];
				$arrUpload['size'] = $_FILES[$uploadfile]['size'];
				$arrUpload['path'] = $_FILES[$uploadfile]['tmp_name'];
				$arrUpload['type'] = self::checkUploadFileType($arrUpload['name'], self::$arrMineType[$_FILES[$uploadfile]['type']]);
			}
		}
		return $arrUpload;
	}
	
	public static function moveUploadFlie($uploadfile, $touploadfile) {
		return move_uploaded_file($uploadfile, $touploadfile);
 	}
	
	public static function checkUploadFileType($file, $type) {
		$arrFile = explode('.', $file);
		$filetype = '.' . $arrFile[count($arrFile) - 1];
		if(strpos($type, $filetype) !== false) {
			return $filetype;
		}
		return false;
	}
 }
 ?>
