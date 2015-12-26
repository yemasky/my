<?php
if( !defined('DEFINE_PHP') ){
define('DEFINE_PHP','YES');

/// physical path ///
define('__ROOT_PATH',substr(dirname(__FILE__), 0, -6));
define('__ROOT_TPLS_TPATH',__ROOT_PATH.'templates/');
define('__ROOT_TEMPLATES_TPATH', __ROOT_TPLS_TPATH);
define('__ROOT_LOGS_PATH',__ROOT_PATH.'logs/');


// web charset, language
define('__CHARSET','utf-8');
define('__LANGUAGE','zh-CN');
require_once(__ROOT_PATH . "/libs/Core/func.Common.php");
require_once(__ROOT_PATH . "/libs/Core/BaseAction.class.php");
}
?>