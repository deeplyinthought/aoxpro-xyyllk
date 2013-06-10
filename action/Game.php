<?php

class My_Action_Game extends My_Action_Abstract {
	private $_weiboService = null;

	protected function _preAction() {
		if(strtolower($this->getServer('REQUEST_METHOD')) == 'post') {
			$sign = $this->getRequest('signed_request');
			if(!empty($sign)){
				$o = new SaeTOAuthV2(WB_AKEY , WB_SKEY);
				$data = $o->parseSignedRequest($sign);
				if($data == '-2'){
					$this->_exit('签名错误!');
				} else {
					$_SESSION['oauth2'] = $data;
				}
			}
		}

		if (empty($_SESSION['oauth2']["user_id"])) {
			$this->_actionName = 'auth';
		} else {
			$this->_weiboService = new SaeTClientV2( 
					WB_AKEY, 
					WB_SKEY,
					$_SESSION['oauth2']['oauth_token'],
					''
					);
		} 

		$this->setSession($_SESSION);
	}

	protected function _postAction() {}

	public function indexAction() {}

	public function authAction() {}

	public function loginAction() {
		try {
			$sParams = $this->getSession('oauth2');
			$user = $this->_weiboService->show_user_by_id($sParams['user_id']);
			if(empty($user)) {
				throw Exception('get user failed');
			}
			$ret = My_Model_User::insertUpdate(
					$user['id'], 
					$user['screen_name'],
					time()
					);
			$this->setViewParams(
					'data', 
					array('success' => !empty($ret) ? 1 : 0)
					);
		} catch (Exception $e) {}
	}

	public function infoAction() {
		try {
			$sParams = $this->getSession('oauth2');
			$user = My_Model_User::getByWeiboId($sParams['user_id']);
		} catch (Exception $e) {}

		$this->setViewParams('data', array(
					'success' => !empty($user) ? 1 : 0,
					'weibo_name' => !empty($user) ? $user[0]->weibo_name : '',
					'high_score' => !empty($user) ? $user[0]->high_score : '',
					'title' => !empty($user) ? My_Service_Game::getTitle($user[0]->high_score) : '',
					));
	}
}
