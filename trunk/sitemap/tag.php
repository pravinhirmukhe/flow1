<?php
//tag sitemap


//引入图片插件入口文件
require_once('../wp-content/plugins/nextgen-gallery/ngg-config.php');
require_once('../inc/php/cfg.php');


$args = array('orderby' => 'term_id', 'order' => 'DESC',
		'hide_empty' => true, 'exclude' => '', 'include' => '',
		'number' => '50000', 'fields' => 'all', 'slug' => '', 'parent' => '',
		'hierarchical' => true, 'child_of' => 0, 'get' => '', 'name__like' => '',
		'pad_counts' => false, 'offset' => '', 'search' => '');

$tags=get_terms('ngg_tag',$args);

echo $head="<?xml version='1.0' encoding='UTF-8'?>
	<urlset xmlns=\"http://www.google.com/schemas/sitemap/0.84\"
	xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\"
	xsi:schemaLocation=\"http://www.google.com/schemas/sitemap/0.84
	http://www.google.com/schemas/sitemap/0.84/sitemap.xsd\">";

foreach($tags as $tag){
	$url = '<url>';
	$url.= '<loc>'.$cfg['siteurl'].'tag/'.$tag->term_id.'-'.$tag->slug.'.html</loc>';
	$url.= '<changefreq>weekly</changefreq>';
	$url.= '<priority>';
	if(10 <= $tag->count && $tag->count < 100){
		$url.= '0.6';
	}elseif(100 <= $tag->count && $tag->count < 200){
		$url.= '0.7';
	}elseif($tag->count >= 200){
		$url.= '0.8';
	}else{
		$url.= '0.5';
	}
	$url.= '</priority>';
	$url.= '</url>';
	echo $url;
}

echo $foot="</urlset>";
?>