<?php
//引入图片插件入口文件
require_once('./wp-content/plugins/nextgen-gallery/ngg-config.php');
require_once('./inc/php/cfg.php');
$tags=get_terms('ngg_tag');
foreach($tags as $tag){
	$tag->link = $cfg['siteurl'].'tag/'.$tag->term_id.'-'.$tag->slug.'.html';
}
$args=array(
	'smallest' => 20, 'largest' => 100, 'unit' => 'pt', 'number' => 100,
	'format' => 'flat', 'orderby' => 'count', 'order' => 'RAND'
	);
$tags_display=wp_generate_tag_cloud($tags,$args);
//页面title
$page_title='FOTO';
$title='Tags'.$cfg['sitetitle'];
require_once './inc/html/head.html'; ?>
<div id='main'>
<?php echo $tags_display; ?>
</div>
<?php
require_once './inc/html/foot.html';?>