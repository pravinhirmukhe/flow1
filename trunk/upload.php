<?php
//引入图片插件入口文件
require_once('./wp-content/plugins/nextgen-gallery/ngg-config.php');
require_once('./wp-content/plugins/nextgen-gallery/ngg-config.php');
require_once('./inc/php/cfg.php');

/*
config for this page
*/
//图集路径
$gallerypath='wp-content/gallery/temp';
//图集id
$gid=10;

//
//
//更改文件权限属性函数
function foto_chmod($filename = '') {
	// Set correct file permissions (taken from wp core)
	$stat = @ stat(dirname($filename));
	$perms = $stat['mode'] & 0007777;
	$perms = $perms & 0000666;
	if ( @chmod($filename, $perms) )
		return true;
		
	return false;
}

//不显示失败信息
$display='none';

if($_SERVER ["REQUEST_METHOD"] == 'POST'){
	if($_FILES['image']['size'] > 1024000){
		$display='block';
		$errormsg='sorry,your foto is more than 1M Byte';
	}elseif(empty($_POST['title']) or empty($_POST['description']) or empty($_POST['tags'])){
		$display='block';
		$errormsg='Please fill in all of the foto information';
	}elseif($_FILES['image']['error'] == 4){
		$display='block';
		$errormsg='Please choose your foto';
		}
	else{
		$title=strip_tags($_POST['title']);
		$description=addslashes(strip_tags($_POST['description']));
		$tags=addslashes(strip_tags($_POST['tags']));
		if ($_FILES['image']['error'] == 0) {
			$temp_file = $_FILES['image']['tmp_name'];
			$filepart = pathinfo ( strtolower($_FILES['image']['name']) );
			// required until PHP 5.2.0
			$filepart['filename'] = substr($filepart["basename"],0 ,strlen($filepart["basename"]) - (strlen($filepart["extension"]) + 1) );
			
			$filename = time().sanitize_title($filepart['filename']) . '.' . $filepart['extension'];

			// check for allowed extension
			$ext = array('jpeg', 'jpg', 'png', 'gif'); 
			if (!in_array($filepart['extension'],$ext)){ 
				$display='block';
				$errormsg='sorry,the file is no valid image file!';
			}else{
			
				$dest_file = WINABSPATH . $gallerypath . '/' . $filename;
				
				// save temp file to gallery
				if (!@move_uploaded_file($_FILES['image']['tmp_name'], $dest_file)){
					nggGallery::show_error(__('Error, the file could not moved to : ','nggallery').$dest_file);
					continue;
				} 
				if (!foto_chmod ($dest_file)) {
					nggGallery::show_error(__('Error, the file permissions could not set','nggallery'));
					continue;
				}
				$pid=nggdb::insert_image($gid, $filename, $title, $description, 1);
				$tags = explode(",", $tags);
				wp_set_object_terms($pid, $tags, 'ngg_tag');
				$wpdb->query( "UPDATE $wpdb->nggpictures SET imagedate = now()  WHERE pid = $pid");
				$display='block';
				$errormsg='your upload is successful,it will be reviewed.you can continue';
			}
		}
	}
}
//输出页头keywords
$keywords=$cfg['keywords'];

//输出页面description
$description=$cfg['description'];

//输出$title
$page_title=$cfg['sitename'];
$title='Upload'.$cfg['sitetitle'];
require_once './inc/html/head.html';
?>
<style media="screen" type="text/css">
.upload{
	width:600px;
	margin:0 auto;
	text-align:left;
}
.button-primary {
	font-family: "Lucida Grande", "Bitstream Vera Sans", Verdana, Arial, sans-serif;
	text-decoration: none;
	font-size: 11px !important;
	line-height: 16px;
	padding: 2px 8px;
	cursor: pointer;
	border-width: 1px;
	border-style: solid;
	-moz-border-radius: 11px;
	-khtml-border-radius: 11px;
	-webkit-border-radius: 11px;
	border-radius: 11px;
background:#21759B url(./wp-admin/images/button-grad.png) repeat-x scroll left top;
border-color:#298CBA !important;
color:#FFFFFF !important;
font-weight:bold;
}
.button-primary:hover{
	border-color: #13455b !important;
	color: #EAF2FA !important;
}
.button-primary:active {
background:#21759B url(./wp-admin/images/button-grad-active.png) repeat-x scroll left top;
border-color:#13455B;
color:#000000;
}
textarea, input, select {
-x-system-font:none;
font-family:"Lucida Grande",Verdana,Arial,"Bitstream Vera Sans",sans-serif;
font-size:13px;
font-size-adjust:none;
font-stretch:normal;
font-style:normal;
font-variant:normal;
font-weight:normal;
line-height:19px;
margin:1px;}
input[type="file"], input[type="text"], textarea{border-color:#DFDFDF;-moz-border-radius-bottomleft:6px;
-moz-border-radius-bottomright:6px;
-moz-border-radius-topleft:6px;
-moz-border-radius-topright:6px;
border-style:solid;
border-width:1px;
font-size:1.7em;
outline-color:-moz-use-text-color;
outline-style:none;
outline-width:medium;
padding:5px}
textarea{width:100%}
h2{text-align:center; font-weight:bold; font-size:18px}
.errormsg{
	background-color:#FFFBCC;
	border-color:#E6DB55;
	padding:5px;
	-moz-border-radius-bottomleft:3px;
	-moz-border-radius-bottomright:3px;
	-moz-border-radius-topleft:3px;
	-moz-border-radius-topright:3px;
	border-style:solid;
border-width:1px;
	color:#333333;
	font-family:"Lucida Grande",Verdana,Arial,"Bitstream Vera Sans",sans-serif;
font-size:13px;
font-size-adjust:none;
font-style:normal;
font-variant:normal;
font-weight:normal;
line-height:normal;
	}
</style>
<div id='main' class='upload'>
<h2>Upload</h2>
<div class='errormsg' style="display:<?php echo $display; ?>"><p><?php echo $errormsg; ?></p></div>
	<form name="foto" method="POST" enctype="multipart/form-data" action="" accept-charset="utf-8" >
		<p>choose your foto(<STRONG>The limit your upload is 1M Byte</STRONG>):</p>
		<p><input type="file" name="image" /></p>
		<p>title:</p>
		<p><input type="text" value="" name="title"/></p>
		<p>tags(comma separated list): (example: tag1,tag2,tag3)</p>
		<p><input type="text" value="" name="tags"/></p>
		<p>description:</p>
		<p><textarea tabindex="8" rows="3" name="description"></textarea></p>
		<p><input class="button-primary" type="submit" name="upload" value="upload foto" /></p>
	</form>
</div>
<?php
require_once './inc/html/foot.html';?>
