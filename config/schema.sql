DROP DATABASE `xyyllk`;
CREATE DATABASE `xyyllk`;

USE `xyyllk`;

CREATE TABLE `user` (
  `id` bigint NOT NULL AUTO_INCREMENT COMMENT '用户自增ID',
  `weibo_id` bigint NOT NULL DEFAULT '0' COMMENT '用户微博ID',
  `weibo_name` varchar(64) NOT NULL DEFAULT '微博用户' COMMENT '微博用户名',
  `high_score` bigint NOT NULL DEFAULT '0' COMMENT '用户总得分',
  `login_time` bigint NOT NULL DEFAULT '0' COMMENT '用户上次参与游戏时间',
  `create_time` bigint NOT NULL DEFAULT '0' COMMENT '用户第一次参与游戏时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `weibo_id_idx` (`weibo_id`),
  KEY `total_high_idx` (`high_score`),
  KEY `create_time_idx` (`create_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户信息表'; 

CREATE TABLE `user_status` (
  `id` bigint NOT NULL AUTO_INCREMENT COMMENT '流水ID',
  `weibo_id` bigint NOT NULL DEFAULT '0' COMMENT '用户微博ID',
  `total_score` bigint NOT NULL DEFAULT '0' COMMENT '用户总得分',
  `status` smallint NOT NULL DEFAULT '0' COMMENT '用户状态， 0 空闲;1 游戏中',
  `level` smallint NOT NULL DEFAULT '0' COMMENT '用户当前难度，0-4',
  `level_time` bigint NOT NULL DEFAULT '0' COMMENT '当时难度进入时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `weibo_id_idx` (`weibo_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户状态表'; 

CREATE TABLE `bonus_quota` (
  `id` smallint NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `quota` bigint NOT NULL DEFAULT '5' COMMENT '爆奖日配额',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='爆奖配额表';

CREATE TABLE `bonus_user` (
  `id` smallint NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `weibo_id` bigint NOT NULL DEFAULT '0' COMMENT '用户微博ID',
  `bonus_time` bigint NOT NULL DEFAULT '0' COMMENT '用户爆奖时间',
  PRIMARY KEY (`id`),
  KEY `weibo_id_idx` (`weibo_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='爆奖用户表';

CREATE TABLE `action_log` (
  `id` bigint NOT NULL AUTO_INCREMENT COMMENT '流水ID',
  `weibo_id` bigint NOT NULL DEFAULT '0' COMMENT '用户ID',
  `action_name` varchar(16) NOT NULL DEFAULT '' COMMENT '动作名',
  `action_body` text NOT NULL DEFAULT '' COMMENT '动作内容',
  `action_time` bigint NOT NULL DEFAULT '0' COMMENT '动作时间',
  PRIMARY KEY (`id`),
  KEY `weibo_id_idx` (`weibo_id`),
  KEY `action_time_idx` (`action_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户动作日志';
