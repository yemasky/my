<?php
/**
 *-------------------------
 *
 * The show the detail of each product
 *
 * PHP versions 5

**/
namespace www;

class IndexAction extends \BaseAction {
	
	protected function check($objRequest, $objResponse) {

	}

	protected function service($objRequest, $objResponse) {
		switch($objRequest->getAction()) {
			case 'writereview':
				$this->doWriteReview($objRequest, $objResponse);
			break;
			default:
				$this->doBase($objRequest, $objResponse);
			break;
		}
	}

	/**
	 * 首页显示 
	 */
	protected function doBase($objRequest, $objResponse) {
		//赋值
		//设置类别
		$objResponse -> nav = 'index';
		//设置Meta(共通)
		$objResponse -> setTplValue("__Meta", \BaseCommon::getMeta('index', '我的网站', '我的网站', '我的网站'));
		$objResponse -> setTplName("www/base");
	}
	



}
?>