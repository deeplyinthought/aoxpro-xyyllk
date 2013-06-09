<?php

abstract class My_Action_Abstract {
	protected $_params = array();
	protected $_response = array();

	public function __construct(array $params = array()) {
		$this->setParams($params);
	}

	public function setParams($params) {
		$this->_params = $params;
		return $this;
	}

	public function getParams($key = null) {
		if(is_null($key)) {
			return $this->_params;
		}
		return isset($this->_params[$key]) ? $this->_params[$key] : null;
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
		$this->doAction();
		$this->sendData();
	}

	abstract public function doAction();
}
