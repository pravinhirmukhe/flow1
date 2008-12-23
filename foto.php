<?php
require_once('./wp-content/plugins/nextgen-gallery/ngg-config.php');
require_once('./inc/php/cfg.php');

if(!empty($_GET['pid'])){
	$foto=nggdb::find_image($_GET['pid']);
	$tags=wp_get_object_terms($_GET['pid'],'ngg_tag');
}else{
	$fotos=nggdb::get_random_images();
	$foto=$fotos[0];
	$tags=wp_get_object_terms($foto->pid,'ngg_tag');
	}
$foto_url=str_replace($cfg['trueurl'],$cfg['baseurl'],$foto->imageURL);
//输出$title
$title=$foto->alttext.$cfg['sitetitle'];
require_once './inc/html/head.html';
?>
<div id='main'>
	<div class="guanggao"></div>
  <p> <a href="foto.php" title="rand foto"> <img class="alignnone" title="<?php echo $foto->alttext; ?>" src="<?php echo $foto_url; ?>" alt="<?php echo $foto->alttext; ?>"> </a></p>
  <a href="foto.php?pid=<?php echo $foto->pid - 1; ?>">« Previous</a> <a href="foto.php">Random</a> <a href="foto.php?pid=<?php echo $foto->pid + 1; ?>">Next »</a>
  <p>Tags:
  	<?php if (!empty($tags)) { foreach($tags as $tag){ ?>
	    <a href="tags.php?tag=<?php echo $tag->term_id; ?>"><?php echo $tag->slug; ?></a>
		<?php } } else{ echo 'none'; } ?>
  </p>
	<div class="guanggao"></div>
</div>
<?php 
require_once './inc/html/foot.html';?>