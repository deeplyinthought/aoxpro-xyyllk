<?php

class My_Action_Game extends My_Action_Abstract {
	private $_weiboService = null;

	public function loginAction() {
		try {
			$sParams = $this->getSession('oauth2');
			$user = $this->_weiboService->show_user_by_id($sParams['user_id']);
			if(empty($user)) {
				throw new Exception;
			}
			$ret = My_Model_User::insertUpdate(
					$user['id'], 
					$user['name'],
					$this->getActionTime()
					);
			My_Model_UserStatus::deleteByWeiboId($sParams['user_id']);
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

	public function playAction() {
		$sParams = $this->getSession('oauth2');
		$conn = My_Model_Base::getInstance()->getConnection();
		try {
			$conn->beginTransaction();

			$ret = My_Model_UserStatus::insertUpdate(
					$sParams['user_id'], 
					$this->getActionTime()
					);
			if(empty($ret)) {
				throw new Exception;
			}
			$status = My_Model_UserStatus::getByWeiboId($sParams['user_id']);

			if(!My_Model_BonusUser::hasBonus($sParams['user_id'])) {
				($bonus = $this->_getBonus()) && My_Model_BonusUser::create(
						$sParams['user_id'],
						$this->getActionTime()
						);
			}

			$conn->commit();
		} catch (Exception $e) {
			$conn->rollBack();
		}

		$this->setViewParams('data', array(
					'success' => !empty($status) ? 1 : 0,
					'level' => !empty($status) ? $status[0]->level : '',
					'total_score' => !empty($status) ? $status[0]->total_score : '',
					'bonus' => !empty($bonus) ? 1 : 0,
					));
	}

	public function passAction() {
	}

	public function shareAction() {
		$sParams = $this->getSession('oauth2');
		$avatarId = $this->getRequest('avatar_id');
		$followers = $this->_weiboService->followers_by_id($sParams['user_id'], 0, 200);
		if(!empty($followers) 
				&& !empty($followers['users'])) {
			shuffle($followers['users']);
		}
		$flrAr = array();
		for($i = 0; $i < 3; $i++) {
			if(isset($followers['users'][$i])) {
				$follower = $followers['users'][$i];
				if(empty($follower['name'])) {
					$follower['name'] = $follower['id'];
				}
				$flrAr[] = '@'.$follower['name'];
			}
		}
		$this->_weiboService->upload(
				ConfigLoader::getInstance()->get('share', 'content') . implode(' ', $flrAr),
				sprintf("%s/%02d.jpg", ConfigLoader::getInstance()->get('share', 'pic_url'), $avatarId)
				);
		$this->setViewParams('data', array('success' => 1));
	}

	public function indexAction() {}

	public function authAction() {}

	protected function _postAction() {}

	protected function _preAction() {
		$this->_verifySign();
		$this->_verifyAuth();
	}

	private function _verifyAuth() {
		$sessOauth = $this->getSession('oauth2');
		if (empty($sessOauth['user_id'])) {
			$this->_actionName = 'auth';
		} else {
			$this->_weiboService = new SaeTClientV2( 
					WB_AKEY, 
					WB_SKEY,
					$sessOauth['oauth_token'],
					''
					);
		} 
	}

	private function _verifySign() {
		if(strtolower($this->getServer('REQUEST_METHOD')) == 'post') {
			$sign = $this->getRequest('signed_request');
			if(!empty($sign)){
				$o = new SaeTOAuthV2(WB_AKEY , WB_SKEY);
				$data = $o->parseSignedRequest($sign);
				if($data == '-2'){
					$this->_exit('签名错误!');
				} else {
					$_SESSION['oauth2'] = $data;
					$this->setSession($_SESSION);
				}
			}
		}
	}

	private function _getBonus() {
		$bonus = false;
		$gameCfg = ConfigLoader::getInstance()->get('game');
		$r = rand(1, $gameCfg['bonus_rate']);
		if($r <= $gameCfg['bonus_quota']) {
			$bonus = My_Model_BonusQuota::getQuota();
		}
		return $bonus;
	}

}
