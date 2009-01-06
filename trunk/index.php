<?php
//引入图片插件入口文件
require_once('./wp-content/plugins/nextgen-gallery/ngg-config.php');
require_once('./inc/php/cfg.php');

//获取最后一张图片
$fotos=nggdb::find_last_images(0,1);
$foto=$fotos[0];

//在session里保存最后一个id
if($_SESSION['last_pid'] <= 0 or $_SESSION['last_pid']!=$foto->pid){
	$_SESSION['last_pid']=$foto->pid;
}
//获取图片tags
$tags=wp_get_object_terms($foto->pid,'ngg_tag');

//修改图片链接为分流网址
$foto_url=str_replace($cfg['trueurl'],$cfg['baseurl'],$foto->imageURL);

//输出页头keywords
$keywords=$cfg['keywords'];
$keywords=$foto->name.','.$keywords;

//输出页面description
$description=$cfg['description'];

//输出$title
$page_title=$cfg['sitename'];
$title=$cfg['sitename'].$cfg['sitetitle'];
require_once './inc/html/head.html';
?>
<div id='main'>
  <p><?php echo $foto->imagedate; ?></p>
  <p><a href="/foto.php" title="rand foto"><img title="<?php echo $foto->alttext; ?>" src="<?php echo $foto_url; ?>" alt="<?php echo $foto->alttext; ?>"></a></p>
  <p><?php if (!empty($tags)) { foreach($tags as $tag){ ?>
	    <a href="tag/<?php echo $tag->term_id.'-'.$tag->slug; ?>.html"><?php echo $tag->slug; ?></a>
		<?php } } else{ echo 'none'; } ?>
  </p>
</div>
<?php
require_once './inc/html/foot.html';?>
