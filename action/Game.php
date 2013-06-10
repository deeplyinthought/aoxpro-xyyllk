<?php

class My_Action_Game extends My_Action_Abstract {
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
			if(!empty($ret)) {
				$this->setResponse(array('success' => 1));
			}
		} catch (Exception $e) {}
	}

	public function infoAction() {
		try {
			$sParams = $this->getSession('oauth2');
			$user = My_Model_User::getByWeiboId($sParams['user_id']);
			if(!empty($user)) {
				$this->setResponse(array(
							'success' => 1,
							'weibo_name' => $user[0]->weibo_name,
							'high_score' => $user[0]->high_score,
							'title' => My_Service_Game::getTitle($user[0]->high_score),
							));
			}
		} catch (Exception $e) {}
	}
}
