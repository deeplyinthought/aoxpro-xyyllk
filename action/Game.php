<?php

class My_Action_Game extends My_Action_Abstract {
	private $_weiboService = null;
	private $_exception = null;

	public function loginAction() {
		$ret = null;
		try {
			$sParams = $this->getSession('oauth2');
			$user = $this->_weiboService->show_user_by_id($sParams['user_id']);
			if(empty($user)) {
				throw new Exception('get user error');
			}
			$ret = My_Model_User::insertUpdate(
					$user['id'], 
					$user['name'],
					$this->getActionTime()
					);
			My_Model_UserStatus::deleteByWeiboId($sParams['user_id']);
		} catch (Exception $e) {
			$this->_exception = $e;
		}

		$this->setViewParams(
				'data', 
				array('success' => !empty($ret) ? 1 : 0)
				);
	}

	public function infoAction() {
		$user = null;
		try {
			$sParams = $this->getSession('oauth2');
			$user = My_Model_User::getByWeiboId($sParams['user_id']);
			if(empty($user)) {
				throw new Exception('get user error');
			}
		} catch (Exception $e) {
			$this->_exception = $e;
		}

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
			$user = My_Model_User::getByWeiboId($sParams['user_id']);
			if(empty($user)) {
				throw new Exception('get user error');
			}
			$ret = My_Model_UserStatus::insertUpdate(
					$sParams['user_id'], 
					$this->getActionTime()
					);
			if(empty($ret)) {
				throw new Exception('update user status error');
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
			$this->_exception = $e;
			$conn->rollBack();
		}

		$this->setViewParams('data', array(
					'success' => !empty($status) ? 1 : 0,
					'level' => !empty($status) ? $status[0]->level : 0,
					'total_score' => !empty($status) ? $status[0]->total_score : 0,
					'bonus' => !empty($bonus) ? 1 : 0,
					));
	}

	public function passAction() {
		$status = null;
		$rank = 0;
		$sParams = $this->getSession('oauth2');
		$rt = $this->getRequest('rt');
		$score = 0;

		$conn = My_Model_Base::getInstance()->getConnection();
		try {
			$conn->beginTransaction();
			if(is_null($rt) || $rt === '') {
				$rt = 0;
				throw new Exception('remaining time invalid');
			}
			$status = My_Model_UserStatus::getByWeiboId($sParams['user_id']);
			if(empty($status) || empty($status[0])) {
				throw new Exception('get user status error');
			}
			if($status[0]->status != My_Model_UserStatus::STATUS_PLAY) {
				throw new Exception('user not play');
			}
			if($this->getActionTime() - $status[0]->level_time + $rt <= ConfigLoader::getInstance()->get('game', 'total_time')) {
				throw new Exception('rt error');
			}
			$score = My_Service_Game::getScore($status[0]->level, $rt);
			if($score == 0) {
				throw new Exception('count score error');
			}
			$status[0]->total_score += $score;
			$status[0]->level += 1;
			$status[0]->level_time = $this->getActionTime();
			$status[0]->status = My_Model_UserStatus::STATUS_IDLE;
			if(!My_Model_UserStatus::updateUserStatus($status[0])) {
				throw new Exception('update user status error');
			}
			if(!My_Model_User::updateUserHighScore($sParams['user_id'], $status[0]->total_score)) {
				throw new Exception('update user high score error');
			}
			$conn->commit();
		} catch (Exception $e) {
			$this->_exception = $e;
			$status = null;
			$conn->rollBack();
		}

		$this->setViewParams('data', array(
					'success' => !empty($status) ? 1 : 0,
					'level_score' => !empty($status) ? $score : 0,
					'total_score' => !empty($status) ? $status[0]->total_score : 0,
					'rank' => !empty($status) ? $rank : 0,
					'level_next' => !empty($status) ? $status[0]->level : 0
					));
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

	protected function _postAction() {
		$sParams = $this->getSession('oauth2');
		$actionBody = sprintf(
				'ip=%s|sid=%s|msg=%s',
				My_Service_Game::getIP(),
				session_id(),
				is_null($this->_exception) ? 'done' : $this->_exception->getMessage()
				);
		My_Model_ActionLog::logAction(
				$sParams['user_id'],
				$this->getActionName(),
				$actionBody,
				$this->getActionTime()
				);
	}

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
