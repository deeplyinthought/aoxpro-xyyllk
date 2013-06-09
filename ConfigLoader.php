<?php
defined('WWW_ROOT') ? '' : define('WWW_ROOT', dirname(__FILE__) . '/htdocs');
defined('APP_ROOT') ? '' : define('APP_ROOT', dirname(__FILE__));
define('WB_AKEY' , '826923995');
define('WB_SKEY' , '2b0732658f04b0c712fa4334aa94cfb1');
define('CANVAS_PAGE' , 'http://apps.weibo.com/aoxproxyyllk');
define('CLASS_PREFIX', 'My_');

function  __autoload($class) {
	if(strncasecmp($class, CLASS_PREFIX, strlen(CLASS_PREFIX)) === 0) {
		list($prefix, $dir, $classname) = explode('_', $class);
		$dir = strtolower($dir);
		return require_once(APP_ROOT . "/$dir/$classname.php");
	}

	require_once(APP_ROOT . '/lib/saetv2.ex.class.php');
}

class ConfigLoader {
	private static $_instance = null;
	private $_gConfig = array();

	private function __construct() {
		include_once(APP_ROOT . '/config/config.php');
		$this->_gConfig = $gConfig;
	}

	public static function getInstance() {
		if(is_null(self::$_instance)) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	public function get($class = null, $item = null) {
		if(is_null($class)) {
			return $this->_gConfig;
		}
		if(is_null($item)) {
			return $this->_gConfig[$class];
		}
		return $this->_gConfig[$class][$item];
	}
}

ConfigLoader::getInstance();
