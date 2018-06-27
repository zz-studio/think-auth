/*
 MySQL Data Transfer

 Source Server         : Zz-Admin
 Source Server Type    : MySQL
 Source Server Version : 50638
 Source Host           : 127.0.0.1:3306
 Source Schema         : zzadmin_db
 File Encoding         : 65001

 Date: 27/06/2018 10:38:35
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for sys_auth_role
-- ----------------------------
DROP TABLE IF EXISTS `sys_auth_role`;
CREATE TABLE `sys_auth_role` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` char(100) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='权限组';

-- ----------------------------
-- Table structure for sys_auth_role_rule
-- ----------------------------
DROP TABLE IF EXISTS `sys_auth_role_rule`;
CREATE TABLE `sys_auth_role_rule` (
  `rule_id` int(11) unsigned NOT NULL,
  `role_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`rule_id`,`role_id`),
  UNIQUE KEY `uid_group_id` (`rule_id`,`role_id`),
  KEY `uid` (`rule_id`),
  KEY `group_id` (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='规则及权限对应表';

-- ----------------------------
-- Table structure for sys_auth_role_user
-- ----------------------------
DROP TABLE IF EXISTS `sys_auth_role_user`;
CREATE TABLE `sys_auth_role_user` (
  `user_id` bigint(20) unsigned NOT NULL,
  `role_id` int(11) unsigned NOT NULL,
  UNIQUE KEY `uid_group_id` (`user_id`,`role_id`),
  KEY `uid` (`user_id`),
  KEY `group_id` (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='用户及权限对应表';

-- ----------------------------
-- Table structure for sys_auth_rule
-- ----------------------------
DROP TABLE IF EXISTS `sys_auth_rule`;
CREATE TABLE `sys_auth_rule` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` char(80) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `title` char(20) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `type` tinyint(1) NOT NULL DEFAULT '1',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `condition` char(100) CHARACTER SET utf8 NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=57 DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='权限路由表';

SET FOREIGN_KEY_CHECKS = 1;
