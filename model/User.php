<?php

class My_Model_User {
	public static function insertUpdate($weiboId, $weiboName, $timestamp) {
		$res = My_Model_Base::getInstance()->query(
				'INSERT INTO `user` (`weibo_id`, `weibo_name`, `login_time`, `create_time`) VALUES (:weibo_id, :weibo_name, :login_time, :create_time) ON DUPLICATE KEY UPDATE `weibo_name`=:weibo_name, `login_time` = :login_time',
				array(
					':weibo_id' => $weiboId, 
					':weibo_name' => $weiboName, 
					':login_time' => $timestamp, 
					':create_time' => $timestamp
				     )
				);
		return !$res || $res->rowCount() 
			? true
			: false;
	}

	public static function getByWeiboId($weiboId) {
		$res = My_Model_Base::getInstance()->query(
				'SELECT * FROM `user` WHERE `weibo_id` = :weibo_id',
				array(
					':weibo_id' => $weiboId
				     )
				);
		return !$res || $res->rowCount() 
			? $res->fetchAll(PDO::FETCH_CLASS)
			: false;
	}

	public static function updateUserHighScore($weiboId, $score) {
		$res = My_Model_Base::getInstance()->query(
				'UPDATE `user` SET `high_score` = :score WHERE `weibo_id` = :weibo_id AND `high_score` < :score',
				array(
					':score' => $score,
					':weibo_id' => $weiboId
				     )
				);
		return empty($res) ? false : true ;
	}
}

