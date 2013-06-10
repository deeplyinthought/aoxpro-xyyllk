<?php

class My_Model_BonusQuota {
	public static function getQuota() {
		$res = My_Model_Base::getInstance()->query(
				'UPDATE `bonus_quota` SET `quota` = `quota` - 1 WHERE `quota` > 0',
				array()
				);
		return !$res || $res->rowCount()
			? true
			: false;
	}
}

