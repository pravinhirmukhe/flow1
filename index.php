<?php
//引入图片插件入口文件
require_once('./wp-content/plugins/nextgen-gallery/ngg-config.php');
require_once('./inc/php/cfg.php');
//获取最后一张图片
$fotos=nggdb::find_last_images(0,1);
$foto=$fotos[0];
//获取图片tags
$tags=wp_get_object_terms($foto->pid,'ngg_tag');
//修改图片链接为分流网址
$foto_url=str_replace($cfg['trueurl'],$cfg['baseurl'],$foto->imageURL);

//输出页头keywords
if(!empty($tags)){
	$key_arr=array();
	foreach($tags as $tag){
		$key_arr[]=$tag->slug;
		$keywords=implode(',',$key_arr);
	}
}else{
	$keywords=$cfg['keywords'];
}

//输出页面description
$description=empty($foto->$description)?$cfg['description']:$foto->$description;

//输出$title
$title='INDEX'.$cfg['sitetitle'];
require_once './inc/html/head.html';
?>
<div id='main'>
  <p><?php echo $foto->imagedate; ?></p>
  <p> <a href="foto.php" title="rand foto"> <img title="<?php echo $foto->alttext; ?>" src="<?php echo $foto_url; ?>" alt="<?php echo $foto->alttext; ?>"> </a></p>
  <p>Tags:
  	<?php if (!empty($tags)) { foreach($tags as $tag){ ?>
	    <a href="tags.php?tag=<?php echo $tag->term_id; ?>"><?php echo $tag->slug; ?></a>
		<?php } } else{ echo 'none'; } ?>
  </p>
</div>
<?php
require_once './inc/html/foot.html';?>
