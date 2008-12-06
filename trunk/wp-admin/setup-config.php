<?php
/**
 * Retrieves and creates the wp-config.php file.
 *
 * The permissions for the base directory must allow for writing files in order
 * for the wp-config.php to be created using this page.
 *
 * @package WordPress
 * @subpackage Administration
 */

/**
 * We are installing.
 *
 * @package WordPress
 */
define('WP_INSTALLING', true);

/**#@+
 * These three defines are required to allow us to use require_wp_db() to load
 * the database class while being wp-content/db.php aware.
 * @ignore
 */
define('ABSPATH', dirname(dirname(__FILE__)).'/');
define('WPINC', 'wp-includes');
define('WP_CONTENT_DIR', ABSPATH . 'wp-content');
/**#@-*/

require_once('../wp-includes/compat.php');
require_once('../wp-includes/functions.php');
require_once('../wp-includes/classes.php');

if (!file_exists('../wp-config-sample.php'))
	wp_die('抱歉，安装程序需要 wp-config-sample.php 文件开始工作。请从 WordPress 安装包内重新上传此文件。');
//	wp_die('Sorry, I need a wp-config-sample.php file to work from. Please re-upload this file from your WordPress installation.');

$configFile = file('../wp-config-sample.php');

if ( !is_writable('../'))
	wp_die("抱歉，我无法写入到此目录。你可以修改 WordPress 目录的权限，或者手动创建 wp-config.php 文件。");
//	wp_die("Sorry, I can't write to the directory. You'll have to either change the permissions on your WordPress directory or create your wp-config.php manually.");

// Check if wp-config.php has been created
if (file_exists('../wp-config.php'))
	wp_die("<p>文件 'wp-config.php' 已存在。如果你要重设此文件中的任何设置，需要首先删除此文件。你也可以尝试 <a href='install.php'>立即安装</a>。</p>");
//	wp_die("<p>The file 'wp-config.php' already exists. If you need to reset any of the configuration items in this file, please delete it first. You may try <a href='install.php'>installing now</a>.</p>");

// Check if wp-config.php exists above the root directory
if (file_exists('../../wp-config.php') && ! file_exists('../../wp-load.php'))
	wp_die("<p>文件 'wp-config.php' 已存在于 WordPress 安装目录。如果你要重设此文件中的任何设置，首先要删除此文件。你也可以尝试 <a href='install.php'>立即安装</a>。</p>");
//	wp_die("<p>The file 'wp-config.php' already exists one level above your WordPress installation. If you need to reset any of the configuration items in this file, please delete it first. You may try <a href='install.php'>installing now</a>.</p>");

if (isset($_GET['step']))
	$step = $_GET['step'];
else
	$step = 0;

/**
 * Display setup wp-config.php file header.
 *
 * @ignore
 * @since 2.3.0
 * @package WordPress
 * @subpackage Installer_WP_Config
 */
function display_header() {
	header( 'Content-Type: text/html; charset=utf-8' );
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>WordPress &rsaquo; 安装配置文件</title>
<link rel="stylesheet" href="<?php echo $admin_dir; ?>css/install.css" type="text/css" />

</head>
<body>
<h1 id="logo"><img alt="WordPress" src="images/wordpress-logo.png" /></h1>
<?php
}//end function display_header();

switch($step) {
	case 0:
		display_header();
?>

<p>欢迎使用 WordPress。在开始前，我们要设置一些数据库的信息。安装前，你要清楚以下信息。</p>
<ol>
	<li>数据库名称</li>
	<li>数据库用户名</li>
	<li>数据库密码</li>
	<li>数据库主机</li>
	<li>数据表前缀（如果你想在一个数据库里运行多个 WordPress 的话）</li>
</ol>
	
<p><strong>如果因为某些原因，自动创建文件操作不能工作，不要担心。所有的这些操作只是往配置文件里面添加数据库的信息。您可以使用文本编辑器打开 <code>wp-config-sample.php</code>，填入对应信息，然后保存为 <code>wp-config.php</code>。</strong></p>
<p>在所有的情况中，这些信息最可能由您的 ISP（互联网服务提供商） 提供。如果您没有这些信息，需要您联系您的 ISP（互联网服务提供商），然后再继续。如果您已经准备好，</p>

<p class="step"><a href="setup-config.php?step=1" class="button">我们开始吧！</a></p>
<?php
	break;

	case 1:
		display_header();
	?>
<form method="post" action="setup-config.php?step=2">
	<p>您应当在下面输入数据库细节信息。如果您不确定，联系您的主机服务商。</p>
	<table class="form-table">
		<tr>
			<th scope="row">数据库名称</th>
			<td><input name="dbname" type="text" size="25" value="wordpress" /></td>
			<td>您想要把 WP 安装到的数据库的名称。</td>
		</tr>
		<tr>
			<th scope="row">用户名</th>
			<td><input name="uname" type="text" size="25" value="username" /></td>
			<td>您的 MySQL 数据库用户名。</td>
		</tr>
		<tr>
			<th scope="row">密码</th>
			<td><input name="pwd" type="text" size="25" value="password" /></td>
			<td>...和 MySQL 数据库密码。</td>
		</tr>
		<tr>
			<th scope="row">数据库主机</th>
			<td><input name="dbhost" type="text" size="25" value="localhost" /></td>
			<td>99% 的情况下，您不必修改这个值。</td>
		</tr>
		<tr>
			<th scope="row">数据表前缀</th>
			<td><input name="prefix" type="text" id="prefix" value="wp_" size="25" /></td>
			<td>如果您想要在一个数据库里安装多个 WordPress，修改这个值。</td>
		</tr>
	</table>
	<p class="step"><input name="submit" type="submit" value="提交" class="button" /></p>
</form>

<?php
	break;

	case 2:
	$dbname  = trim($_POST['dbname']);
	$uname   = trim($_POST['uname']);
	$passwrd = trim($_POST['pwd']);
	$dbhost  = trim($_POST['dbhost']);
	$prefix  = trim($_POST['prefix']);
	if (empty($prefix)) $prefix = 'wp_';

	// Test the db connection.
	/**#@+
	 * @ignore
	 */
	define('DB_NAME', $dbname);
	define('DB_USER', $uname);
	define('DB_PASSWORD', $passwrd);
	define('DB_HOST', $dbhost);
	/**#@-*/

	// We'll fail here if the values are no good.
	require_wp_db();
	if ( !empty($wpdb->error) )
		wp_die($wpdb->error->get_error_message());

	$handle = fopen('../wp-config.php', 'w');

	foreach ($configFile as $line_num => $line) {
		switch (substr($line,0,16)) {
			case "define('DB_NAME'":
				fwrite($handle, str_replace("putyourdbnamehere", $dbname, $line));
				break;
			case "define('DB_USER'":
				fwrite($handle, str_replace("'usernamehere'", "'$uname'", $line));
				break;
			case "define('DB_PASSW":
				fwrite($handle, str_replace("'yourpasswordhere'", "'$passwrd'", $line));
				break;
			case "define('DB_HOST'":
				fwrite($handle, str_replace("localhost", $dbhost, $line));
				break;
			case '$table_prefix  =':
				fwrite($handle, str_replace('wp_', $prefix, $line));
				break;
			default:
				fwrite($handle, $line);
		}
	}
	fclose($handle);
	chmod('../wp-config.php', 0666);

	display_header();
?>
<p>万事俱备！您已经通过了安装的第一部分。WordPress 现在可以和您的数据库建立联系了。如果您已做好准备，现在就开始</p>

<p><a href="install.php" class="button">运行安装程序</a></p>
<?php
	break;
}
?>
</body>
</html>
