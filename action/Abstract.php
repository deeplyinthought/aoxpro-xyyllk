<?php

abstract class My_Action_Abstract {
	protected $_request = array();

	protected $_session = array();

	protected $_response = array();

	protected $_weiboService = null;

	public function __construct($weiboService) {
		$this->_request = $_REQUEST;
		$this->_session = $_SESSION;
		$this->_weiboService = $weiboService;
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

	public function setResponse($response) {
		$this->_response = $response;
		return $this;
	}

	public function sendData() {
		$resAr = '';
		foreach($this->_response as $name => $value) {
			$resAr[] = "$name=" . urlencode($value);
		}
		echo implode('&', $resAr);
	}

	public function process() {
		$actionName = $this->getRequest('action') . 'Action';
		if(method_exists($this, $actionName)) {
			$this->$actionName();
			$this->sendData();
		}
	}
}
