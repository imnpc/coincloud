/*
 Navicat Premium Data Transfer

 Source Server         : L_Homestead_本机数据库
 Source Server Type    : MySQL
 Source Server Version : 80028
 Source Host           : localhost:33060
 Source Schema         : coincloud

 Target Server Type    : MySQL
 Target Server Version : 80028
 File Encoding         : 65001

 Date: 20/07/2022 14:29:13
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for admin_config
-- ----------------------------
DROP TABLE IF EXISTS `admin_config`;
CREATE TABLE `admin_config` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `admin_config_name_unique` (`name`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of admin_config
-- ----------------------------
BEGIN;
INSERT INTO `admin_config` (`id`, `name`, `value`, `description`, `created_at`, `updated_at`) VALUES (1, '__configx__', 'do not delete', '{\"user.reg_qrcode\":{\"options\":[],\"element\":\"image\",\"help\":\"\\u6ce8\\u518c\\u5b8c\\u6210\\u4ee5\\u540e\\u7528\\u6237\\u4e2d\\u5fc3\\u7684APP\\u4e0b\\u8f7d\\u4e8c\\u7ef4\\u7801\",\"name\":\"APP\\u4e0b\\u8f7d\\u4e8c\\u7ef4\\u7801\",\"order\":5},\"user.download_url\":{\"options\":[],\"element\":\"normal\",\"help\":\"\\u7528\\u6237\\u6ce8\\u518c\\u5b8c\\u6bd5\\u4ee5\\u540e\\u9ed8\\u8ba4APP\\u4e0b\\u8f7d\\u5730\\u5740\",\"name\":\"\\u9ed8\\u8ba4APP\\u4e0b\\u8f7d\\u5730\\u5740\",\"order\":10},\"order.card_number\":{\"options\":[],\"element\":\"normal\",\"help\":\"\\u94f6\\u884c\\u5361\\u53f7\",\"name\":\"\\u94f6\\u884c\\u5361\\u53f7\",\"order\":5},\"order.card_name\":{\"options\":[],\"element\":\"normal\",\"help\":\"\\u94f6\\u884c\\u5361\\u5f00\\u6237\\u4eba\\u59d3\\u540d\",\"name\":\"\\u5f00\\u6237\\u4eba\\u59d3\\u540d\",\"order\":10},\"order.account_with_bank\":{\"options\":[],\"element\":\"normal\",\"help\":\"\\u94f6\\u884c\\u5361\\u5f00\\u6237\\u884c\",\"name\":\"\\u5f00\\u6237\\u884c\",\"order\":15},\"order.wallet_usdt_address\":{\"options\":[],\"element\":\"normal\",\"help\":\"USDT\\u6536\\u6b3e\\u7684\\u94b1\\u5305\\u5730\\u5740\",\"name\":\"USDT \\u94b1\\u5305\\u5730\\u5740\",\"order\":20},\"order.wallet_usdt_qrcode\":{\"options\":[],\"element\":\"image\",\"help\":\"USDT\\u6536\\u6b3e\\u7684\\u94b1\\u5305\\u4e8c\\u7ef4\\u7801\\u56fe\\u7247\",\"name\":\"USDT \\u94b1\\u5305\\u4e8c\\u7ef4\\u7801\",\"order\":25},\"withdraw.min\":{\"options\":[],\"element\":\"normal\",\"help\":\"\\u6700\\u5c0f\\u63d0\\u5e01\\u91d1\\u989d\",\"name\":\"\\u6700\\u5c0f\\u63d0\\u5e01\\u91d1\\u989d\",\"order\":5},\"withdraw.coin_fee\":{\"options\":[],\"element\":\"normal\",\"help\":\"\\u63d0\\u5e01\\u624b\\u7eed\\u8d39\",\"name\":\"\\u63d0\\u5e01\\u624b\\u7eed\\u8d39\",\"order\":10},\"upload.image_ext\":{\"options\":[],\"element\":\"tags\",\"help\":\"\\u5141\\u8bb8\\u56fe\\u7247\\u540e\\u7f00\",\"name\":\"\\u5141\\u8bb8\\u56fe\\u7247\\u540e\\u7f00\",\"order\":5},\"app.banner1\":{\"options\":[],\"element\":\"image\",\"help\":\"APP\\u9996\\u9875Banner\\u56fe\\u72471\",\"name\":\"Banner\\u56fe\\u72471\",\"order\":5},\"app.banner2\":{\"options\":[],\"element\":\"image\",\"help\":\"APP\\u9996\\u9875Banner\\u56fe\\u72472\",\"name\":\"Banner\\u56fe\\u72472\",\"order\":10},\"app.banner3\":{\"options\":[],\"element\":\"image\",\"help\":\"APP\\u9996\\u9875Banner\\u56fe\\u72473\",\"name\":\"Banner\\u56fe\\u72473\",\"order\":15}}', '2021-05-06 17:45:10', '2021-05-27 11:06:03');
INSERT INTO `admin_config` (`id`, `name`, `value`, `description`, `created_at`, `updated_at`) VALUES (2, 'user.reg_qrcode', 'images/11cab97e21f2e7a5bd17cb76a6925f39.png', 'APP下载二维码', '2021-05-08 14:40:29', '2021-06-28 13:56:02');
INSERT INTO `admin_config` (`id`, `name`, `value`, `description`, `created_at`, `updated_at`) VALUES (3, 'user.download_url', 'https://down.1024kuangchi.com/xingheyun.apk', '默认APP下载地址', '2021-05-08 14:41:04', '2021-06-28 13:52:56');
INSERT INTO `admin_config` (`id`, `name`, `value`, `description`, `created_at`, `updated_at`) VALUES (4, 'order.card_number', '6230580000286017855', '银行卡号', '2021-05-11 14:16:12', '2021-05-11 14:19:35');
INSERT INTO `admin_config` (`id`, `name`, `value`, `description`, `created_at`, `updated_at`) VALUES (5, 'order.card_name', '卢万里', '开户人姓名', '2021-05-11 14:16:58', '2021-05-11 14:19:35');
INSERT INTO `admin_config` (`id`, `name`, `value`, `description`, `created_at`, `updated_at`) VALUES (6, 'order.account_with_bank', '平安银行上海松江支行', '开户行', '2021-05-11 14:17:21', '2021-05-11 14:19:35');
INSERT INTO `admin_config` (`id`, `name`, `value`, `description`, `created_at`, `updated_at`) VALUES (7, 'order.wallet_usdt_address', 'TNgdEvmQB8Eoz9z8R7zhxKfuRrTW2AQD6K', 'USDT 钱包地址', '2021-05-11 14:18:11', '2021-07-06 22:35:58');
INSERT INTO `admin_config` (`id`, `name`, `value`, `description`, `created_at`, `updated_at`) VALUES (8, 'order.wallet_usdt_qrcode', 'images/f23e648a9a732046dd54b72886eb67c6.png', 'USDT 钱包二维码', '2021-05-11 14:18:37', '2021-07-06 22:35:58');
INSERT INTO `admin_config` (`id`, `name`, `value`, `description`, `created_at`, `updated_at`) VALUES (9, 'withdraw.min', '1', '最小提币金额', '2021-05-25 14:39:31', '2021-05-25 14:39:31');
INSERT INTO `admin_config` (`id`, `name`, `value`, `description`, `created_at`, `updated_at`) VALUES (10, 'withdraw.coin_fee', '0.1', '提币手续费', '2021-05-25 14:39:56', '2021-05-25 14:40:09');
INSERT INTO `admin_config` (`id`, `name`, `value`, `description`, `created_at`, `updated_at`) VALUES (11, 'app.banner1', 'images/009223761f17694b0a888104951209af.png', 'Banner图片1', '2021-05-27 11:04:42', '2021-07-03 16:12:13');
INSERT INTO `admin_config` (`id`, `name`, `value`, `description`, `created_at`, `updated_at`) VALUES (12, 'upload.image_ext', 'png,jpg,jpeg,gif,bmp,8', '允许图片后缀', '2021-05-27 10:38:38', '2021-05-27 15:43:04');
INSERT INTO `admin_config` (`id`, `name`, `value`, `description`, `created_at`, `updated_at`) VALUES (13, 'app.banner2', 'images/f710ad6f7d4aeeacbee6f71e52cb9e8c.jpg', 'Banner图片2', '2021-05-27 11:04:42', '2021-07-03 16:13:21');
INSERT INTO `admin_config` (`id`, `name`, `value`, `description`, `created_at`, `updated_at`) VALUES (14, 'app.banner3', 'images/236f2f268a5d867d707abcb032c52210.jpg', 'Banner图片3', '2021-05-27 11:04:42', '2021-07-03 16:12:13');
COMMIT;

-- ----------------------------
-- Table structure for admin_menu
-- ----------------------------
DROP TABLE IF EXISTS `admin_menu`;
CREATE TABLE `admin_menu` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int NOT NULL DEFAULT '0',
  `order` int NOT NULL DEFAULT '0',
  `title` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `icon` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `uri` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `permission` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of admin_menu
-- ----------------------------
BEGIN;
INSERT INTO `admin_menu` (`id`, `parent_id`, `order`, `title`, `icon`, `uri`, `permission`, `created_at`, `updated_at`) VALUES (1, 0, 1, '桌面', 'fa-bar-chart', '/', NULL, NULL, '2021-05-06 18:08:49');
INSERT INTO `admin_menu` (`id`, `parent_id`, `order`, `title`, `icon`, `uri`, `permission`, `created_at`, `updated_at`) VALUES (2, 0, 25, '系统管理', 'fa-tasks', NULL, NULL, NULL, '2021-06-19 09:04:33');
INSERT INTO `admin_menu` (`id`, `parent_id`, `order`, `title`, `icon`, `uri`, `permission`, `created_at`, `updated_at`) VALUES (3, 2, 26, '管理员', 'fa-users', 'auth/users', NULL, NULL, '2021-06-19 09:04:33');
INSERT INTO `admin_menu` (`id`, `parent_id`, `order`, `title`, `icon`, `uri`, `permission`, `created_at`, `updated_at`) VALUES (4, 2, 27, '角色', 'fa-user', 'auth/roles', NULL, NULL, '2021-06-19 09:04:33');
INSERT INTO `admin_menu` (`id`, `parent_id`, `order`, `title`, `icon`, `uri`, `permission`, `created_at`, `updated_at`) VALUES (5, 2, 28, '权限', 'fa-ban', 'auth/permissions', NULL, NULL, '2021-06-19 09:04:33');
INSERT INTO `admin_menu` (`id`, `parent_id`, `order`, `title`, `icon`, `uri`, `permission`, `created_at`, `updated_at`) VALUES (6, 2, 29, '菜单', 'fa-bars', 'auth/menu', NULL, NULL, '2021-06-19 09:04:33');
INSERT INTO `admin_menu` (`id`, `parent_id`, `order`, `title`, `icon`, `uri`, `permission`, `created_at`, `updated_at`) VALUES (7, 2, 30, '日志', 'fa-history', 'auth/logs', NULL, NULL, '2021-06-19 09:04:33');
INSERT INTO `admin_menu` (`id`, `parent_id`, `order`, `title`, `icon`, `uri`, `permission`, `created_at`, `updated_at`) VALUES (8, 0, 31, 'Helpers', 'fa-gears', '', NULL, '2021-05-06 17:03:51', '2021-06-19 09:04:33');
INSERT INTO `admin_menu` (`id`, `parent_id`, `order`, `title`, `icon`, `uri`, `permission`, `created_at`, `updated_at`) VALUES (9, 8, 32, 'Scaffold', 'fa-keyboard-o', 'helpers/scaffold', NULL, '2021-05-06 17:03:51', '2021-06-19 09:04:33');
INSERT INTO `admin_menu` (`id`, `parent_id`, `order`, `title`, `icon`, `uri`, `permission`, `created_at`, `updated_at`) VALUES (10, 8, 33, 'Database terminal', 'fa-database', 'helpers/terminal/database', NULL, '2021-05-06 17:03:51', '2021-06-19 09:04:33');
INSERT INTO `admin_menu` (`id`, `parent_id`, `order`, `title`, `icon`, `uri`, `permission`, `created_at`, `updated_at`) VALUES (11, 8, 34, 'Laravel artisan', 'fa-terminal', 'helpers/terminal/artisan', NULL, '2021-05-06 17:03:51', '2021-06-19 09:04:33');
INSERT INTO `admin_menu` (`id`, `parent_id`, `order`, `title`, `icon`, `uri`, `permission`, `created_at`, `updated_at`) VALUES (12, 8, 35, 'Routes', 'fa-list-alt', 'helpers/routes', NULL, '2021-05-06 17:03:51', '2021-06-19 09:04:33');
INSERT INTO `admin_menu` (`id`, `parent_id`, `order`, `title`, `icon`, `uri`, `permission`, `created_at`, `updated_at`) VALUES (13, 0, 36, '计划任务', 'fa-clock-o', 'scheduling', NULL, '2021-05-06 17:07:25', '2021-06-19 09:04:33');
INSERT INTO `admin_menu` (`id`, `parent_id`, `order`, `title`, `icon`, `uri`, `permission`, `created_at`, `updated_at`) VALUES (14, 0, 37, '系统设置', 'fa-toggle-on', 'configx/edit', NULL, '2021-05-06 17:45:01', '2021-06-19 09:04:33');
INSERT INTO `admin_menu` (`id`, `parent_id`, `order`, `title`, `icon`, `uri`, `permission`, `created_at`, `updated_at`) VALUES (16, 0, 13, '用户', 'fa-users', '/users', NULL, '2021-05-06 18:08:43', '2021-06-01 14:03:04');
INSERT INTO `admin_menu` (`id`, `parent_id`, `order`, `title`, `icon`, `uri`, `permission`, `created_at`, `updated_at`) VALUES (17, 0, 15, '钱包类型', 'fa-university', '/wallet-types', NULL, '2021-05-09 14:22:32', '2021-06-01 14:03:04');
INSERT INTO `admin_menu` (`id`, `parent_id`, `order`, `title`, `icon`, `uri`, `permission`, `created_at`, `updated_at`) VALUES (18, 0, 14, '产品', 'fa-cubes', '/products', NULL, '2021-05-10 15:48:41', '2021-06-01 14:03:04');
INSERT INTO `admin_menu` (`id`, `parent_id`, `order`, `title`, `icon`, `uri`, `permission`, `created_at`, `updated_at`) VALUES (19, 0, 16, '订单管理', 'fa-reorder', '/orders', NULL, '2021-05-11 15:23:09', '2021-06-01 14:03:04');
INSERT INTO `admin_menu` (`id`, `parent_id`, `order`, `title`, `icon`, `uri`, `permission`, `created_at`, `updated_at`) VALUES (20, 0, 3, '分红', 'fa-cloud', '/day-bonuses', NULL, '2021-05-13 10:00:18', '2021-05-24 18:35:17');
INSERT INTO `admin_menu` (`id`, `parent_id`, `order`, `title`, `icon`, `uri`, `permission`, `created_at`, `updated_at`) VALUES (21, 20, 5, '默认数据', 'fa-bars', '/default-day-bonuses', NULL, '2021-05-14 10:31:12', '2021-05-24 18:35:17');
INSERT INTO `admin_menu` (`id`, `parent_id`, `order`, `title`, `icon`, `uri`, `permission`, `created_at`, `updated_at`) VALUES (22, 20, 4, '每日分红', 'fa-cloud', '/day-bonuses', NULL, '2021-05-14 10:32:04', '2021-05-24 18:35:17');
INSERT INTO `admin_menu` (`id`, `parent_id`, `order`, `title`, `icon`, `uri`, `permission`, `created_at`, `updated_at`) VALUES (23, 24, 7, '分成记录', 'fa-calendar', '/user-bonuses', NULL, '2021-05-15 14:30:43', '2021-05-24 18:35:17');
INSERT INTO `admin_menu` (`id`, `parent_id`, `order`, `title`, `icon`, `uri`, `permission`, `created_at`, `updated_at`) VALUES (24, 0, 6, '每日分成', 'fa-bars', '/user-bonuses', NULL, '2021-05-15 14:31:28', '2021-05-24 18:35:17');
INSERT INTO `admin_menu` (`id`, `parent_id`, `order`, `title`, `icon`, `uri`, `permission`, `created_at`, `updated_at`) VALUES (25, 24, 8, '线性释放列表', 'fa-bars', '/freeds', NULL, '2021-05-15 15:06:25', '2021-05-24 18:35:17');
INSERT INTO `admin_menu` (`id`, `parent_id`, `order`, `title`, `icon`, `uri`, `permission`, `created_at`, `updated_at`) VALUES (26, 24, 9, '每日线性释放', 'fa-bars', '/day-freeds', NULL, '2021-05-15 15:06:56', '2021-05-24 18:35:17');
INSERT INTO `admin_menu` (`id`, `parent_id`, `order`, `title`, `icon`, `uri`, `permission`, `created_at`, `updated_at`) VALUES (27, 0, 19, '内容管理', 'fa-newspaper-o', NULL, NULL, '2021-05-24 09:42:35', '2021-06-19 09:04:33');
INSERT INTO `admin_menu` (`id`, `parent_id`, `order`, `title`, `icon`, `uri`, `permission`, `created_at`, `updated_at`) VALUES (28, 27, 20, '公告', 'fa-volume-up', '/announcements', NULL, '2021-05-24 09:43:15', '2021-06-19 09:04:33');
INSERT INTO `admin_menu` (`id`, `parent_id`, `order`, `title`, `icon`, `uri`, `permission`, `created_at`, `updated_at`) VALUES (29, 27, 21, '文章分类', 'fa-bars', '/article-categories', NULL, '2021-05-24 10:17:05', '2021-06-19 09:04:33');
INSERT INTO `admin_menu` (`id`, `parent_id`, `order`, `title`, `icon`, `uri`, `permission`, `created_at`, `updated_at`) VALUES (30, 27, 22, '文章', 'fa-bars', '/articles', NULL, '2021-05-24 10:57:57', '2021-06-19 09:04:33');
INSERT INTO `admin_menu` (`id`, `parent_id`, `order`, `title`, `icon`, `uri`, `permission`, `created_at`, `updated_at`) VALUES (31, 0, 24, '客户端版本', 'fa-android', '/versions', NULL, '2021-05-24 14:06:52', '2021-06-19 09:04:33');
INSERT INTO `admin_menu` (`id`, `parent_id`, `order`, `title`, `icon`, `uri`, `permission`, `created_at`, `updated_at`) VALUES (32, 0, 23, '问题反馈', 'fa-comments', '/feedback', NULL, '2021-05-24 15:08:13', '2021-06-19 09:04:33');
INSERT INTO `admin_menu` (`id`, `parent_id`, `order`, `title`, `icon`, `uri`, `permission`, `created_at`, `updated_at`) VALUES (33, 0, 2, '提币申请', 'fa-cloud-upload', '/withdraws', NULL, '2021-05-24 18:35:12', '2021-05-24 18:35:17');
INSERT INTO `admin_menu` (`id`, `parent_id`, `order`, `title`, `icon`, `uri`, `permission`, `created_at`, `updated_at`) VALUES (34, 0, 10, '充币', 'fa-battery-three-quarters', '/recharges', NULL, '2021-06-01 14:01:16', '2021-06-01 14:01:29');
INSERT INTO `admin_menu` (`id`, `parent_id`, `order`, `title`, `icon`, `uri`, `permission`, `created_at`, `updated_at`) VALUES (35, 34, 11, '充值记录', 'fa-bars', '/recharges', NULL, '2021-06-01 14:02:04', '2021-06-01 14:03:04');
INSERT INTO `admin_menu` (`id`, `parent_id`, `order`, `title`, `icon`, `uri`, `permission`, `created_at`, `updated_at`) VALUES (36, 34, 12, '充值封装记录', 'fa-bars', '/recharge-account-logs', NULL, '2021-06-01 14:02:58', '2021-06-01 14:03:04');
INSERT INTO `admin_menu` (`id`, `parent_id`, `order`, `title`, `icon`, `uri`, `permission`, `created_at`, `updated_at`) VALUES (37, 0, 17, '统计', 'fa-area-chart', NULL, NULL, '2021-06-19 09:03:22', '2021-06-19 09:04:01');
INSERT INTO `admin_menu` (`id`, `parent_id`, `order`, `title`, `icon`, `uri`, `permission`, `created_at`, `updated_at`) VALUES (38, 37, 18, '每周统计报表', 'fa-calendar', '/weeklies', NULL, '2021-06-19 09:04:31', '2021-06-19 09:05:08');
COMMIT;

-- ----------------------------
-- Table structure for admin_permissions
-- ----------------------------
DROP TABLE IF EXISTS `admin_permissions`;
CREATE TABLE `admin_permissions` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `http_method` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `http_path` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `admin_permissions_name_unique` (`name`) USING BTREE,
  UNIQUE KEY `admin_permissions_slug_unique` (`slug`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of admin_permissions
-- ----------------------------
BEGIN;
INSERT INTO `admin_permissions` (`id`, `name`, `slug`, `http_method`, `http_path`, `created_at`, `updated_at`) VALUES (1, 'All permission', '*', '', '*', NULL, NULL);
INSERT INTO `admin_permissions` (`id`, `name`, `slug`, `http_method`, `http_path`, `created_at`, `updated_at`) VALUES (2, 'Dashboard', 'dashboard', 'GET', '/', NULL, NULL);
INSERT INTO `admin_permissions` (`id`, `name`, `slug`, `http_method`, `http_path`, `created_at`, `updated_at`) VALUES (3, 'Login', 'auth.login', '', '/auth/login\r\n/auth/logout', NULL, NULL);
INSERT INTO `admin_permissions` (`id`, `name`, `slug`, `http_method`, `http_path`, `created_at`, `updated_at`) VALUES (4, 'User setting', 'auth.setting', 'GET,PUT', '/auth/setting', NULL, NULL);
INSERT INTO `admin_permissions` (`id`, `name`, `slug`, `http_method`, `http_path`, `created_at`, `updated_at`) VALUES (5, 'Auth management', 'auth.management', '', '/auth/roles\r\n/auth/permissions\r\n/auth/menu\r\n/auth/logs', NULL, NULL);
INSERT INTO `admin_permissions` (`id`, `name`, `slug`, `http_method`, `http_path`, `created_at`, `updated_at`) VALUES (6, 'Admin helpers', 'ext.helpers', '', '/helpers/*', '2021-05-06 17:03:51', '2021-05-06 17:03:51');
INSERT INTO `admin_permissions` (`id`, `name`, `slug`, `http_method`, `http_path`, `created_at`, `updated_at`) VALUES (7, 'Scheduling', 'ext.scheduling', '', '/scheduling*', '2021-05-06 17:07:25', '2021-05-06 17:07:25');
INSERT INTO `admin_permissions` (`id`, `name`, `slug`, `http_method`, `http_path`, `created_at`, `updated_at`) VALUES (8, 'Admin Configx', 'ext.configx', '', '/configx/*', '2021-05-06 17:45:01', '2021-05-06 17:45:01');
INSERT INTO `admin_permissions` (`id`, `name`, `slug`, `http_method`, `http_path`, `created_at`, `updated_at`) VALUES (9, 'Admin Config', 'ext.config', '', '/config*', '2021-05-06 18:03:20', '2021-05-06 18:03:20');
COMMIT;

-- ----------------------------
-- Table structure for admin_role_menu
-- ----------------------------
DROP TABLE IF EXISTS `admin_role_menu`;
CREATE TABLE `admin_role_menu` (
  `role_id` int NOT NULL,
  `menu_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  KEY `admin_role_menu_role_id_menu_id_index` (`role_id`,`menu_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of admin_role_menu
-- ----------------------------
BEGIN;
INSERT INTO `admin_role_menu` (`role_id`, `menu_id`, `created_at`, `updated_at`) VALUES (1, 2, NULL, NULL);
COMMIT;

-- ----------------------------
-- Table structure for admin_role_permissions
-- ----------------------------
DROP TABLE IF EXISTS `admin_role_permissions`;
CREATE TABLE `admin_role_permissions` (
  `role_id` int NOT NULL,
  `permission_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  KEY `admin_role_permissions_role_id_permission_id_index` (`role_id`,`permission_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of admin_role_permissions
-- ----------------------------
BEGIN;
INSERT INTO `admin_role_permissions` (`role_id`, `permission_id`, `created_at`, `updated_at`) VALUES (1, 1, NULL, NULL);
COMMIT;

-- ----------------------------
-- Table structure for admin_role_users
-- ----------------------------
DROP TABLE IF EXISTS `admin_role_users`;
CREATE TABLE `admin_role_users` (
  `role_id` int NOT NULL,
  `user_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  KEY `admin_role_users_role_id_user_id_index` (`role_id`,`user_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of admin_role_users
-- ----------------------------
BEGIN;
INSERT INTO `admin_role_users` (`role_id`, `user_id`, `created_at`, `updated_at`) VALUES (1, 1, NULL, NULL);
COMMIT;

-- ----------------------------
-- Table structure for admin_roles
-- ----------------------------
DROP TABLE IF EXISTS `admin_roles`;
CREATE TABLE `admin_roles` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `admin_roles_name_unique` (`name`) USING BTREE,
  UNIQUE KEY `admin_roles_slug_unique` (`slug`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of admin_roles
-- ----------------------------
BEGIN;
INSERT INTO `admin_roles` (`id`, `name`, `slug`, `created_at`, `updated_at`) VALUES (1, 'Administrator', 'administrator', '2021-05-06 16:57:21', '2021-05-06 16:57:21');
COMMIT;

-- ----------------------------
-- Table structure for admin_users
-- ----------------------------
DROP TABLE IF EXISTS `admin_users`;
CREATE TABLE `admin_users` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(190) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `avatar` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `google_auth` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_open_google_auth` tinyint DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `admin_users_username_unique` (`username`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of admin_users
-- ----------------------------
BEGIN;
INSERT INTO `admin_users` (`id`, `username`, `password`, `name`, `avatar`, `remember_token`, `created_at`, `updated_at`, `google_auth`, `is_open_google_auth`) VALUES (1, 'admin', '$2y$10$9bBDnRwfuAx1GbFXHWTZGODm14DaPRL.Nz07u9JQcxvqzjaMH7Jq2', 'Administrator', NULL, 'e3N6VxxuLQhGX5FsJZnYRy22gH3OKkgGpR6wedi8EfNLLYC8wulGnFJF7upO', '2021-05-06 16:57:21', '2021-05-06 16:57:21', NULL, NULL);
COMMIT;

-- ----------------------------
-- Table structure for announcements
-- ----------------------------
DROP TABLE IF EXISTS `announcements`;
CREATE TABLE `announcements` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '标题',
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '内容',
  `is_recommand` tinyint NOT NULL DEFAULT '0' COMMENT '是否推荐 0-否 1-是',
  `status` tinyint NOT NULL DEFAULT '0' COMMENT '是否显示 0-不显示 1-显示',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='公告';

-- ----------------------------
-- Records of announcements
-- ----------------------------
BEGIN;
INSERT INTO `announcements` (`id`, `title`, `content`, `is_recommand`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES (1, '试运营公告', '<p>经过全体技术人员的不懈努力，7月初开始分布式存储的综合管理系统开始进入试运营阶段，感谢所有客户的信任和支持，我们将竭诚的为广大客户提供更加专业和全方位的服务。<br/></p>', 1, 1, '2021-05-24 09:44:32', '2021-09-11 15:00:38', NULL);
INSERT INTO `announcements` (`id`, `title`, `content`, `is_recommand`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES (2, '新版APP系统试运营系统上线 2', '<p>全新的综合式分布存储系统正式进入试运营阶段。<br/></p>', 1, 1, '2021-05-28 10:32:45', '2021-09-11 15:07:26', NULL);
COMMIT;

-- ----------------------------
-- Table structure for article_categories
-- ----------------------------
DROP TABLE IF EXISTS `article_categories`;
CREATE TABLE `article_categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` tinyint NOT NULL DEFAULT '0' COMMENT '上级 ID',
  `order` tinyint NOT NULL DEFAULT '0' COMMENT '排序',
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '分类名称',
  `icon` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '分类图标',
  `status` tinyint NOT NULL DEFAULT '0' COMMENT '是否显示 0-不显示 1-显示',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='文章分类';

-- ----------------------------
-- Records of article_categories
-- ----------------------------
BEGIN;
INSERT INTO `article_categories` (`id`, `parent_id`, `order`, `title`, `icon`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES (1, 0, 0, '进阶小课堂', 'article/icon/0f960d604b0a8180264528d0c4e4c462.png', 1, '2021-05-24 10:20:24', '2021-09-11 17:21:27', NULL);
INSERT INTO `article_categories` (`id`, `parent_id`, `order`, `title`, `icon`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES (2, 0, 0, '项目简介', 'article/icon/30d65c83dbb3f5fba7e374d4bcb16e97.png', 1, '2021-05-24 10:21:06', '2021-06-28 10:35:21', NULL);
INSERT INTO `article_categories` (`id`, `parent_id`, `order`, `title`, `icon`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES (3, 0, 0, '项目动态', 'article/icon/c35defb4f2a548f30fe186cb0d6f403c.png', 1, '2021-05-24 10:21:34', '2021-06-28 10:35:41', NULL);
INSERT INTO `article_categories` (`id`, `parent_id`, `order`, `title`, `icon`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES (4, 0, 0, '帮助中心', 'article/icon/c205a005e2844e677ffacdc7a7e8e6f1.png', 1, '2021-05-24 10:22:10', '2021-06-28 10:35:50', NULL);
INSERT INTO `article_categories` (`id`, `parent_id`, `order`, `title`, `icon`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES (5, 0, 0, '购买FAQ', NULL, 0, '2021-05-27 16:30:51', '2021-05-27 16:41:07', NULL);
INSERT INTO `article_categories` (`id`, `parent_id`, `order`, `title`, `icon`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES (6, 0, 0, '常见问题', NULL, 0, '2021-05-27 16:41:18', '2021-05-27 16:41:18', NULL);
INSERT INTO `article_categories` (`id`, `parent_id`, `order`, `title`, `icon`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES (7, 0, 0, '关于我们', NULL, 0, '2021-06-01 17:17:05', '2021-06-01 17:17:05', NULL);
COMMIT;

-- ----------------------------
-- Table structure for articles
-- ----------------------------
DROP TABLE IF EXISTS `articles`;
CREATE TABLE `articles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `article_category_id` tinyint NOT NULL COMMENT '文章分类 ID',
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '标题',
  `thumb` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '缩略图',
  `desc` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '内容简介',
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '内容',
  `is_recommand` tinyint NOT NULL DEFAULT '0' COMMENT '是否推荐 0-否 1-是',
  `status` tinyint NOT NULL DEFAULT '0' COMMENT '是否显示 0-不显示 1-显示',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='文章';

-- ----------------------------
-- Records of articles
-- ----------------------------
BEGIN;
INSERT INTO `articles` (`id`, `article_category_id`, `title`, `thumb`, `desc`, `content`, `is_recommand`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES (1, 1, '文章1', 'images/d924d61ede2b66db9d50a8047d9c4261.png', '文章简介', '<p>测试文章内容<br/></p>', 0, 1, '2021-05-24 11:28:35', '2021-05-24 11:28:35', NULL);
INSERT INTO `articles` (`id`, `article_category_id`, `title`, `thumb`, `desc`, `content`, `is_recommand`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES (2, 5, '一、购买算力', NULL, NULL, '<p>购买算力必须预先充值,使用USDT购买<br/></p>', 0, 1, '2021-05-27 16:32:09', '2021-05-27 16:32:09', NULL);
INSERT INTO `articles` (`id`, `article_category_id`, `title`, `thumb`, `desc`, `content`, `is_recommand`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES (3, 5, '二、收益如何结算', NULL, NULL, '<p>收益如何结算</p>', 0, 1, '2021-05-27 16:33:00', '2021-05-27 16:33:00', NULL);
INSERT INTO `articles` (`id`, `article_category_id`, `title`, `thumb`, `desc`, `content`, `is_recommand`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES (4, 5, '三、其他费用', NULL, NULL, '<p>其他费用</p>', 0, 1, '2021-05-27 16:33:25', '2021-05-27 16:33:25', NULL);
INSERT INTO `articles` (`id`, `article_category_id`, `title`, `thumb`, `desc`, `content`, `is_recommand`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES (5, 5, '四、云算力有效期', NULL, NULL, '<p>云算力有效期</p>', 0, 1, '2021-05-27 16:34:37', '2021-05-27 16:34:46', NULL);
INSERT INTO `articles` (`id`, `article_category_id`, `title`, `thumb`, `desc`, `content`, `is_recommand`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES (6, 5, '五、风险提示', NULL, NULL, '<p>风险提示</p>', 0, 1, '2021-05-27 16:35:11', '2021-05-27 16:35:11', NULL);
INSERT INTO `articles` (`id`, `article_category_id`, `title`, `thumb`, `desc`, `content`, `is_recommand`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES (7, 6, '问题一', NULL, NULL, '<p>问题一</p>', 0, 1, '2021-05-27 16:41:52', '2021-05-27 16:41:52', NULL);
INSERT INTO `articles` (`id`, `article_category_id`, `title`, `thumb`, `desc`, `content`, `is_recommand`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES (8, 7, '关于我们', NULL, NULL, '<p>经过全体技术人员的不懈努力，7月初分布式存储的综合管理系统开始进入试运营阶段，我们会根据市场和客户的需求不断的升级和完善这套分布式存储综合管理系统。感谢所有客户的信任和支持，我们将竭诚的为广大客户提供更加专业和全方位的服务。</p>', 0, 1, '2021-06-01 17:17:44', '2021-07-06 23:06:22', NULL);
COMMIT;

-- ----------------------------
-- Table structure for currencies
-- ----------------------------
DROP TABLE IF EXISTS `currencies`;
CREATE TABLE `currencies` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `symbol` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `format` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `exchange_rate` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `currencies_code_index` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of currencies
-- ----------------------------
BEGIN;
INSERT INTO `currencies` (`id`, `name`, `code`, `symbol`, `format`, `exchange_rate`, `active`, `created_at`, `updated_at`) VALUES (1, 'US Dollar', 'USD', '$', '$1,0.00', '1', 0, '2021-07-28 11:47:46', '2021-07-30 12:00:00');
INSERT INTO `currencies` (`id`, `name`, `code`, `symbol`, `format`, `exchange_rate`, `active`, `created_at`, `updated_at`) VALUES (2, 'China Yuan Renminbi', 'CNY', '¥', '¥1,0.00', '6.4599', 0, '2021-07-28 11:47:46', '2021-07-30 12:00:00');
COMMIT;

-- ----------------------------
-- Table structure for oauth_clients
-- ----------------------------
DROP TABLE IF EXISTS `oauth_clients`;
CREATE TABLE `oauth_clients` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `secret` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `provider` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `redirect` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `personal_access_client` tinyint(1) NOT NULL,
  `password_client` tinyint(1) NOT NULL,
  `revoked` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `oauth_clients_user_id_index` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of oauth_clients
-- ----------------------------
BEGIN;
INSERT INTO `oauth_clients` (`id`, `user_id`, `name`, `secret`, `provider`, `redirect`, `personal_access_client`, `password_client`, `revoked`, `created_at`, `updated_at`) VALUES (1, NULL, 'AndroidAPP', 'JqawKTpRHVsbOtQ0vtBNnoqEZye60DBS0sj1r7qO', 'users', 'http://localhost', 0, 1, 0, '2021-05-06 15:10:37', '2021-05-06 15:10:37');
COMMIT;

-- ----------------------------
-- Table structure for versions
-- ----------------------------
DROP TABLE IF EXISTS `versions`;
CREATE TABLE `versions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `platform` tinyint NOT NULL DEFAULT '1' COMMENT '平台 1-Android 2-iOS',
  `version` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '版本号',
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '描述',
  `app` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'APP压缩包',
  `url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '下载地址',
  `status` tinyint NOT NULL DEFAULT '0' COMMENT '是否启用 0-否 1-是',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='客户端版本';

-- ----------------------------
-- Records of versions
-- ----------------------------
BEGIN;
INSERT INTO `versions` (`id`, `platform`, `version`, `description`, `app`, `url`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES (1, 1, '1.0', '更新 1.0 版本', 'download/xingheyun_1.0.apk', NULL, 1, '2021-06-28 13:52:04', '2021-06-28 13:52:08', NULL);
COMMIT;

SET FOREIGN_KEY_CHECKS = 1;
