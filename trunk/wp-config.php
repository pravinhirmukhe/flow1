<?php
/** 
 * WordPress �Ļ��������ļ�
 *
 * ���ļ���������������Ϣ��MySQL ���ݿ⣬���ݱ�ǰ׺����Կ�����ԣ�������·����
 *
 * @package WordPress
 */

// ** MySQL ���ݿ����� - ����Դ����������������ȡ����Ϣ ** //
/** WordPress ���ݿ����� */
define('DB_NAME', 'tfbjcc_flow');

/** MySQL ���ݿ��û��� */
define('DB_USER', 'tfbjcc_flow');

/** MySQL ���ݿ����� */
define('DB_PASSWORD', 'iy:(8}|Z4<8/');

/** MySQL ���ݿ������� */
define('DB_HOST', 'localhost');

/** �������ݿ�������ַ����� */
define('DB_CHARSET', 'utf8');

/** ���ݿ�У���ַ���������㲻ȷ���������޸Ĵ�� */
define('DB_COLLATE', '');

/**#@+
 * ��һ�޶�����֤��Կ��
 *
 * �޸���Щ��һ�޶��Ķ��
 * �����ͨ��������� {@link http://api.wordpress.org/secret-key/1.1/ WordPress.org ��Կ����}
 *
 * @ʼ�� 2.6.0
 */
define('AUTH_KEY', 'put your unique phrase here');
define('SECURE_AUTH_KEY', 'put your unique phrase here');
define('LOGGED_IN_KEY', 'put your unique phrase here');
/**#@-*/

/**
 * WordPress ���ݿ��ǰ׺��
 *
 * ʹ�ò�ͬ�����ݱ�ǰ׺��������һ�����ݿ��ﰲװ��� WordPress ���͡�
 * ֻ��ʹ�����֡���ĸ���»��ߣ�
 */
$table_prefix  = 'flow_';

/**
 * WordPress ���ػ����ԣ�Ĭ��ΪӢ�
 *
 * �޸���������� WordPress ���ػ�����Ӧ�� MO �ļ���ŵ� wp-content/languages��
 * ���磺�� de.mo �ŵ� wp-content/languages ���� WPLANG ���� 'de' ��֧�ֵ��ġ� 
 * ����й���½�û��ļ��������û��������Ѿ����ú� zh_CN��������ġ�
 */
define ('WPLANG', 'zh_CN');

/**
 * WordPress ��ַ �� ������ַ
 *
 * �����ں�̨�޸������Ϣ����������Ҫ���� WordPress ��ַ�벩�͵�ַʱ��
 * ��ͨ���������д������Ӳ���ã������ȶȸ������ݿ������á�
 *
 * ע�⣬���ô˹�����Ҫȥ��ǰ��� // ����ע����ַ���Ҫ����б�� / 
 *
 * @added by WPChina.org
 */
// define('WP_SITEURL', 'http://domainname/wordpress');
// define('WP_HOME', 'http://domainname');

/**
 * WordPress �汾������
 *
 * ���ھ���������Ѷ��ԣ�������Ҫ�汾�����ܡ������������رմ˹��ܡ�
 * ������ n = -1 ʱ��������������/ҳ����޶��汾������Ĭ��ֵ��
 * ������ n = 0 ʱ������0������/ҳ����޶��汾�����رոù��ܣ�
 * ������ n > 0 ʱ������n������/ҳ����޶��汾��
 *
 * @added by WPChina.org
 */
define('WP_POST_REVISIONS', '-1');

/* ����Ϊֹ�����½�ֹ�޸ģ�ף��д����졣 */

/** WordPress absolute path to the Wordpress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
?>
