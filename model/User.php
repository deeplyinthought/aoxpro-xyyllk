<?php

class My_Model_User {
	public static function insertUpdate($weiboId, $weiboName, $timestamp) {
		$res = My_Model_Base::getInstance()->query(
				'INSERT INTO `user` (`weibo_id`, `weibo_name`, `login_time`, `create_time`) VALUES (:weibo_id, :weibo_name, :login_time, :create_time) ON DUPLICATE KEY UPDATE `login_time` = :login_time',
				array(
						':weibo_id' => $weiboId, 
						':weibo_name' => $weiboName, 
						':login_time' => $timestamp, 
						':create_time' => $timestamp
						)
				);
	}
}

