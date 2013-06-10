<?php

class My_Service_Game {
	public static function getScore($level, $rt) {
		$lvScore = array(
				0 => 160,
				1 => 240,
				2 => 300,
				3 => 480,
				4 => 640,
			      );
		$totalTime = 80;
		$totalTimeScore = 2500;

		return isset($lvScore[$level]) 
			? round($lvScore[$level] + $rt / $totalTime * $totalTimeScore)
			: 0;
	}

	public static function getTitle($score) {
		$titleList = array(
				0 => 'title1',
				1 => 'title2',
				2 => 'title3',
				3 => 'title4',
				4 => 'title5',
			      );
		$scoreList = array(1000, 2000, 3400, 5000);

		foreach($scoreList as $lv => $limit) {
			if($score < $limit) {
				return $titleList[$lv];
			}
		}

		return $titleList[4];
	}
}
