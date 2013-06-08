<?php

class My_Db_Access {
	protected static $_instance = null;
	protected $_pdo = null;
	
	private function __construct() {
		$config = ConfigLoader::getInstance()->get('db');
		$this->_pdo = new PDO(
				$config['dsn'],
				$config['user'],
				$config['pass']
				);
		if(empty($this->_pdo)) {
			return null;
		}
		return $this;
	}
	
	public static function getInstance() {
		if(is_null(self::$_instance)) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
}

/* testing code
require_once('../ConfigLoader.php');
Db_Access::getInstance();
* /
