<?php

class My_Model_UserStatus {
	const STATUS_PLAY = 1;
	const STATUS_IDLE = 0;

	public static function insertUpdate($weiboId, $timestamp) {
		$res = My_Model_Base::getInstance()->query(
				'INSERT INTO `user_status` (`weibo_id`, `level_time`) VALUES (:weibo_id, :level_time) ON DUPLICATE KEY UPDATE `weibo_id`=:weibo_id, `level_time` = :level_time, `status` = ' . self::STATUS_PLAY,
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

	public static function updateUserStatus($status) {
		$res = My_Model_Base::getInstance()->query(
				'UPDATE `user_status` SET `total_score` = :total_score, `level` = :level, `level_time` = :level_time, `status` = :status WHERE `id` = :id',
				array(
					':total_score' => $status->total_score,
					':level' => $status->level,
					':level_time' => $status->level_time,
					':status' => $status->status,
					':id' => $status->id,
				     )
				);
		return $res ? $res->rowCount() : false; 
	}
}

