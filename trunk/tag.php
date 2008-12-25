<?php
//引入图片插件入口文件
require_once('./wp-content/plugins/nextgen-gallery/ngg-config.php');
require_once('./inc/php/cfg.php');

if(!empty($_GET['tagid'])){
	$pids=get_objects_in_term($_GET['tagid'],'ngg_tag');
	$fotos=nggdb::find_images_in_list($pids,false,'DESC');
	if(empty($fotos)){
		header('Location: tags.php');
	}
	//页面title
	$page_title=$_GET['tag'];
	$title=$page_title.$cfg['sitetitle'];
}else{
	header('Location: tags.php');
}

require_once './inc/html/head.html'; ?>
<div id='main'>
<?php foreach($fotos as $foto){ ?>
	<a href="foto.php?pid=<?php echo $foto->pid; ?>"><img src="<?php echo fotourl($foto->thumbURL); ?>" alt="<?php echo fotourl($foto->alttext); ?>" /></a>
<?php } ?>
</div>
<?php
require_once './inc/html/foot.html';?>