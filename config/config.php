<?php
$gConfig = array();

$gConfig['db'] = array(
		'dsn' => 'mysql:host=localhost;dbname=xyyllk',
		'user' => 'root',
		'pass' => 'root',
		);

$gConfig['game'] = array(
		'bonus_rate' => 10,
		'bonus_quota' => 5,
		'total_time' => 80,
		'total_time_score' => 2500,
		'max_level' => 4,
		);

$gConfig['share'] = array(
		'pic_url' => 'http://disney.aoxpro.com/images',
		'content' => '没有什么可以阻挡，我享受消除方块儿的快乐时光，白雪公主喜羊羊，我在迪士尼金章连连看里已经过关斩将，现在我已经是 "%s"，每天活动送出一块儿Au999的金章，我还需打败 %s%% 的玩家就有机会拿到了！',
		'content_error' => '没有什么可以阻挡，我享受消除方块儿的快乐时光，白雪公主喜羊羊，我在迪士尼金章连连看里已经过关斩将，每天活动送出一块儿Au999的金章，多多参与就有机会拿到了！'
		);
