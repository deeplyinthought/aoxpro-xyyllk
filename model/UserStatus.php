<?php

class My_Model_UserStatus {
	public static function insertUpdate($weiboId, $timestamp) {
		$res = My_Model_Base::getInstance()->query(
				'INSERT INTO `user_status` (`weibo_id`, `level_time`) VALUES (:weibo_id, :level_time) ON DUPLICATE KEY UPDATE `weibo_id`=:weibo_id, `level_time` = :level_time',
				array(
					':weibo_id' => $weiboId, 
					':level_time' => $timestamp
				     )
				);
		return !$res || $res->rowCount()
			? true
			: false;
	}

	public static function getByWeiboId($weiboId) {
		$res = My_Model_Base::getInstance()->query(
				'SELECT * FROM `user_status` WHERE `weibo_id` = :weibo_id',
				array(
					':weibo_id' => $weiboId
				     )
				);
		return !$res || $res->rowCount() 
			? $res->fetchAll(PDO::FETCH_CLASS)
			: false;
	}

	public static function deleteByWeiboId($weiboId) {
		$res = My_Model_Base::getInstance()->query(
				'DELETE FROM `user_status` WHERE `weibo_id` = :weibo_id',
				array(
					':weibo_id' => $weiboId
				     )
				);
		return $res ? $res->rowCount() : false; 
	}
}

