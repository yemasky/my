<?php
/*
	class.Service.php
	auther: cooc 
	email:yemasky@msn.com
*/

class Service {

	public static function setEtag($pname) {
		header("Etag: ".$pname); 
	} 
	
	public static function getEtag() {
		return $_SERVER['HTTP_IF_NONE_MATCH'];
	} 

	public static function sendEtag($pname) {
		header("Etag: ".$pname, true, 304); 
        exit;
	}  
	
    public static function etag($pname) {
		if(!__ETAG) return;
		if(isset($_SERVER['HTTP_IF_NONE_MATCH'])) {
			if($_SERVER['HTTP_IF_NONE_MATCH'] == $pname) {
				 header("Etag: ".$pname, true, 304); 
				 exit;
			} 
		}
        header("Etag: ".$pname); 
    } 
	
	public static function flushClient() {
		flush();
	}

}
?>