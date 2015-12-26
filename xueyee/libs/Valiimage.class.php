<?php
/*
	class.Utilities.php
	auther: cooc 
	email:yemasky@msn.com
*/

class Valiimage {
	public static function validateCode($length) {
		$array="0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";
		$authnum = "";
		for($i=0;$i<$length;$i++)	{
			$authnum .=substr($array,rand(0,25),1);
		}
		return $authnum; 
	}
	
	public static function validateMd5() {
		return md5(uniqid(rand(), true));
	}

	public static function generateValidationImage($string = NULL, $width = 72, $height = 30, $d = 0, $num = 10) {
		$site_font_path = __ROOT_PATH . "font/font.ttf";
		$len=strlen($string);
		$bordercolor = "#333333";
		$bgcolor = "#FFFFFF";
		$image = imagecreate($width, $height);
		$bordercolor = self::getcolor($image,$bordercolor);
		imagefilledrectangle($image,0,0,$width+1,$height+1,$bordercolor); 
		$back = self::getcolor($image,$bgcolor);
		imagefilledrectangle($image,1,1,$width-2,$height-2,$back);
		
		$image = self::setnoise($image,$width,$height,$num);
		$size = ceil($width / $len); 
		//取得字体
		if($site_font_path) {
		   $font = $site_font_path;
		} else {
		   $font = 5;
		}
		for($i=0;$i<$len;$i++){
			$TempText=substr($string,$i,1);
			$textColor = imagecolorallocate($image, rand(0, 100), rand(0, 100), rand(0, 100));
			$randsize =trim(rand($size-$size/6, $size+$size/6));
			$randAngle = rand(-15,15);
			$x=8+($width-$width/8)/$len*$i;
			$y=rand($height-3,$height-10);
			imagettftext($image,$randsize,$randAngle,$x,$y,$textColor,$font,$TempText);  
		}
		header("Expires: Mon, 23 Jul 1993 05:00:00 GMT");
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");
		header("Content-type: image/png");
		imagejpeg($image);
		imagedestroy($image);
	}
	
	public static function getcolor($image,$color){
		//$color = eregi_replace ("^#","",$color);
		$r = $color[1].$color[2];
		$r = hexdec ($r);
		$b = $color[3].$color[4];
		$b = hexdec ($b);
		$g = $color[5].$color[6];
		$g = hexdec ($g);
		$color = imagecolorallocate ($image, $r, $b, $g); 
		return $color;
	}
	public static function setnoise($image,$width,$height,$noisenum){
		for ($i=0; $i<$noisenum; $i++){
			$randColor = imagecolorallocate($image, rand(0, 255), rand(0, 255), rand(0, 255));
			imagesetpixel($image, rand(0, $width), rand(0, $height), $randColor);
			$line_w1 = rand(0, $width);
			$line_w2 = rand(0, $width);
			$line_h1 = rand(0, $height);
			$line_h2 = rand(0, $height);
			imageline($image, $line_w1, $line_h1, $line_w2, $line_h2, $randColor);
			imageline($image, $line_w1, $line_h1 + 1, $line_w2, $line_h2 + 1, $randColor);
		} 
		return $image;
	}
	
	//缩放图片
	public static function resizeImages($image, $type = NULL, $o_w, $o_h, $p_percent, $p_height = NULL, $setimgtype = 'jpg') {
		$percent_w = '';
		$arrImg = explode('.', $image);
		$filenotype = str_replace($arrImg[count($arrImg) - 1], '', $image);

		if((empty($o_w) && empty($o_h)) || empty($type)) list($o_w, $o_h, $stype) = getimagesize($image);
		$type = empty($type) ? $stype : $type;
		$percent = empty($p_height) ? $p_percent : ($p_percent/$o_w > $p_height/$o_h ? $p_height/$o_h : $p_percent/$o_w);
		$width = $o_w*$percent;
		$height = $o_h*$percent;
		$tempic = imagecreatetruecolor($width, $height);
		if($type == '.jpg') {
			$tmpimage = imagecreatefromjpeg($image);
		} elseif($type == '.gif') {
			$tmpimage = imagecreatefromgif($image);
		} elseif($type == '.png') {
			$tmpimage = imagecreatefrompng($image);
		} else {
			return false;
		}
		@unlink($image);
		imagecopyresized($tempic, $tmpimage, 0, 0, 0, 0, $width, $height, $o_w, $o_h);
		if(!empty($p_height)) {
			$_tempic = imagecreatetruecolor($p_percent, $p_height);
			$white = imagecolorallocate($_tempic, 255, 255, 255);
			imagefilledrectangle($_tempic,0,0,$p_percent,$p_height,$white);
			imagecopy($_tempic, $tempic, ($p_percent - $width)/2, ($p_height - $height)/2, 0, 0, $width, $height);
			$tempic = $_tempic;
		}

		if($setimgtype == 'jpg') {
			imagejpeg($tempic, $filenotype.'jpg', 100);
		}
		if($setimgtype == 'gif') {
			imagegif($tempic, $filenotype.'gif', 100);
		}
		if($setimgtype == 'png') {
			imagepng($tempic, $filenotype.'png', 100);
		}
		imagedestroy($tempic); 
	}
	
	//$groundImage 原图像，$waterPos 水印位置，$baseImage 水印透明图片，$waterImage 水印图片，$tagetImage 目标图像
	public static function rectifyImages($groundImage, $waterPos=0, $baseImage="", $waterImage="", $max_width, $max_height, $tagetImage, $outtype = 'jpg'){ 
		$isWaterImage = FALSE; 
		
		if(!empty($waterImage) && file_exists($waterImage)) { 
			$isWaterImage = TRUE; 
			$water_info = getimagesize($waterImage); 
			$water_w    = $water_info[0];
			$water_h    = $water_info[1];
			switch($water_info[2]){
				case 1:$water_im = imagecreatefromgif($waterImage);break; 
				case 2:$water_im = imagecreatefromjpeg($waterImage);break; 
				case 3:$water_im = imagecreatefrompng($waterImage);break; 
			} 
		} else { 
			return false; 
		} 
		//读取透明图片 
		if(!empty($baseImage) && file_exists($baseImage)) { 
			$base_info = getimagesize($baseImage); 
			$base_w    = $base_info[0];
			$base_h    = $base_info[1];
			switch($base_info[2]){
				case 1:$base_im = imagecreatefromgif($baseImage);break; 
				case 2:$base_im = imagecreatefromjpeg($baseImage);break; 
				case 3:$base_im = imagecreatefrompng($baseImage);break; 
			} 
		}else { 
			return false;; 
		} 
		//读取背景图片 
		if(!empty($groundImage) && file_exists($groundImage)) { 
			$ground_info = getimagesize($groundImage); 
			$ground_w    = $ground_info[0];
			$ground_h    = $ground_info[1];
	
			switch($ground_info[2]){
				case 1:$ground_im = @imagecreatefromgif($groundImage);break; 
				case 2:$ground_im = @imagecreatefromjpeg($groundImage);break; 
				case 3:$ground_im = @imagecreatefrompng($groundImage);break; 
			} 
			if (!$ground_im){
			   return false;  
			}
		}else { 
			return false; 
		} 
	
		if($ground_w <= $max_width && $ground_h <= $max_height){ 
			$dst_width = $ground_w;
			$dst_height = $ground_h;
		}elseif($ground_w/$ground_h >= $max_width/$max_height){ 
			$dst_width = $max_width;
			$dst_height = (int)($max_width*$ground_h/$ground_w);
		}else{ 
			$dst_width = (int)($max_height*$ground_w/$ground_h);
			$dst_height = $max_height;
		}
	
		if($isWaterImage) { 
			$w = $max_width; 
			$h = $max_height; 
			$label = "picture's"; 
		} 
		switch($waterPos) { 
			case 0://随机 
				$posX = rand(0,($w - $dst_width)); 
				$posY = rand(0,($h - $dst_height)); 
				break; 
			case 1://1为顶端居左 
				$posX = 0; 
				$posY = 0; 
				break; 
			case 2://2为顶端居中 
				$posX = ($w - $dst_width) / 2; 
				$posY = 0; 
				break; 
			case 3://3为顶端居右 
				$posX = $w - $dst_width; 
				$posY = 0; 
				break; 
			case 4://4为中部居左 
				$posX = 0; 
				$posY = ($h - $dst_height) / 2; 
				break; 
			case 5://5为中部居中 
				$posX = ($w - $dst_width) / 2; 
				$posY = ($h - $dst_height) / 2; 
				break; 
			case 6://6为中部居右 
				$posX = $w - $dst_width; 
				$posY = ($h - $dst_height) / 2; 
				break; 
			case 7://7为底端居左 
				$posX = 0; 
				$posY = $h - $dst_height; 
				break; 
			case 8://8为底端居中 
				$posX = ($w - $dst_width) / 2; 
				$posY = $h - $dst_height; 
				break; 
			case 9://9为底端居右 
				$posX = $w - $dst_width; 
				$posY = $h - $dst_height; 
				break; 
			default://随机 
				$posX = rand(0,($w - $dst_width)); 
				$posY = rand(0,($h - $dst_height)); 
				break;     
		} 
	
		imagealphablending($base_im, true); 
		if (function_exists("imagecopyresampled")){
			@imagecopyresampled($base_im, $ground_im,$posX,$posY, 0, 0, $dst_width, $dst_height, $ground_w, $ground_h);
		}else{
			@imagecopyresized($base_im, $ground_im,$posX,$posY, 0, 0, $dst_width, $dst_height, $ground_w, $ground_h);
		}
		imagecopy($base_im,$water_im, 0, 0, 0, 0, $water_w,$water_h);
		@unlink($tagetImage); 
		/*switch($ground_info[2]){//取得背景图片的格式,规定的是jpg格式
			case 1:imagegif($base_im,$tagetImage);break; 
			case 2:imagejpeg($base_im,$tagetImage);break; 
			case 3:imagepng($base_im,$tagetImage);break; 
			default:die($errorMsg); 
		}*/ 
		if($outtype == 'jpg') {
			imagejpeg($base_im,$tagetImage);
		}
		if($outtype == 'png') {
			imagepng($base_im,$tagetImage);
		}
		if($outtype == 'gif') {
			imagegif($base_im,$tagetImage);
		}
		//if(!file_exists($tagetImage)){
		//}else{
		//}
		
		if(isset($water_info)) unset($water_info); 
		if(isset($water_im)) imagedestroy($water_im); 
		unset($ground_info); 
		unset($base_info);
		imagedestroy($ground_im); 
		imagedestroy($base_im); 
		return true;
	} 

	
}
?>