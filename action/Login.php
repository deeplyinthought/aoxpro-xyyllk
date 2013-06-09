<?php

class My_Action_Login extends My_Action_Abstract {
	public function doAction() {
		try {
			$ret = My_Model_User::insertUpdate($this->getParams('user_id'), 'Jim', time());
		} catch (Exception $e) {
			$ret = false;
		}
		
		$this->setResponse(array(
					'success' => (empty($ret) ? 0 : 1)
					));
	}
}
