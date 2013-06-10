<?php

abstract class My_Action_Abstract {
	protected $_request = array();

	protected $_session = array();

	protected $_actionName = 'index';

	protected $_actionKey = 'Action';

	protected $_viewParams = '';

	public function __construct() {
		$this->_request = $_REQUEST;
		$this->_session = $_SESSION;
		$this->_server = $_SERVER;
		$this->_response = array('success' => 0);
	}

	public function getRequest($key = null) {
		if(is_null($key)) {
			return $this->_request;
		}
		return isset($this->_request[$key]) ? $this->_request[$key] : null;
	}

	public function getSession($key = null) {
		if(is_null($key)) {
			return $this->_session;
		}
		return isset($this->_session[$key]) ? $this->_session[$key] : null;
	}

	public function setSession($session) {
		$this->_session = $session;
		return $this;
	}

	public function getServer($key = null) {
		if(is_null($key)) {
			return $this->_server;
		}
		return isset($this->_server[$key]) ? $this->_server[$key] : null;
	}

	public function setViewParams($key, $value) {
		$this->_viewParams[$key] = $value;
	}

	public function renderView() {
		$viewScript = APP_ROOT . "/view/$this->_actionName.php";
		$content = '';
		if(file_exists($viewScript)) {
			ob_start();
			include $viewScript;
			$content = ob_get_contents();
			ob_end_clean();
		} else {}
		print $content;
	}

	public function process() {
		$action = $this->getRequest('action') . $this->_actionKey;
		$this->_actionName = method_exists($this, $action) 
			? $this->getRequest('action')
			: $this->_actionName;	

		$this->_preAction();
		$this->{$this->_actionName . $this->_actionKey}();
		$this->_postAction();

		$this->renderView();
	}

	protected function _exit($msg = '') {
		die($msg);
	}

	abstract protected function _preAction();

	abstract protected function _postAction();
}
