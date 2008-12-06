<?php
/** 
 * WordPress 的基本配置文件
 *
 * 此文件包括如下配置信息：MySQL 数据库，数据表前缀，秘钥，语言，及绝对路径。
 *
 * @package WordPress
 */

// ** MySQL 数据库设置 - 你可以从主机服务商那里获取此信息 ** //
/** WordPress 数据库名称 */
define('DB_NAME', 'tfbjcc_flow');

/** MySQL 数据库用户名 */
define('DB_USER', 'tfbjcc_flow');

/** MySQL 数据库密码 */
define('DB_PASSWORD', 'iy:(8}|Z4<8/');

/** MySQL 数据库主机名 */
define('DB_HOST', 'localhost');

/** 创建数据库表所用字符编码 */
define('DB_CHARSET', 'utf8');

/** 数据库校对字符集。如果你不确定，请勿修改此项。 */
define('DB_COLLATE', '');

/**#@+
 * 独一无二的验证密钥。
 *
 * 修改这些独一无二的短语！
 * 你可以通过这个链接 {@link http://api.wordpress.org/secret-key/1.1/ WordPress.org 密钥服务}
 *
 * @始于 2.6.0
 */
define('AUTH_KEY', 'put your unique phrase here');
define('SECURE_AUTH_KEY', 'put your unique phrase here');
define('LOGGED_IN_KEY', 'put your unique phrase here');
/**#@-*/

/**
 * WordPress 数据库表前缀。
 *
 * 使用不同的数据表前缀，可以在一个数据库里安装多个 WordPress 博客。
 * 只能使用数字、字母和下划线！
 */
$table_prefix  = 'flow_';

/**
 * WordPress 本地化语言，默认为英语。
 *
 * 修改这个来进行 WordPress 本地化。对应的 MO 文件须放到 wp-content/languages。
 * 例如：将 de.mo 放到 wp-content/languages 并将 WPLANG 设置 'de' 来支持德文。 
 * 针对中国大陆用户的简体中文用户，下面已经设置好 zh_CN，无须更改。
 */
define ('WPLANG', 'zh_CN');

/**
 * WordPress 网址 与 博客网址
 *
 * 当你在后台修改相关信息出错，或者想要更换 WordPress 地址与博客地址时，
 * 可通过下面两行代码进行硬设置，其优先度高于数据库内设置。
 *
 * 注意，启用此功能需要去掉前面的 // ，并注意网址最后不要带反斜杠 / 
 *
 * @added by WPChina.org
 */
// define('WP_SITEURL', 'http://domainname/wordpress');
// define('WP_HOME', 'http://domainname');

/**
 * WordPress 版本管理功能
 *
 * 对于绝大多数网友而言，并不需要版本管理功能。你可以在这里关闭此功能。
 * 当参数 n = -1 时，保留所有文章/页面的修订版本；这是默认值；
 * 当参数 n = 0 时，保留0次文章/页面的修订版本，即关闭该功能；
 * 当参数 n > 0 时，保留n次文章/页面的修订版本。
 *
 * @added by WPChina.org
 */
define('WP_POST_REVISIONS', '-1');

/* 到此为止，以下禁止修改！祝你写博愉快。 */

/** WordPress absolute path to the Wordpress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
?>
