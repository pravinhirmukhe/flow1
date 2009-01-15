<?php
//foto sitemap


//引入图片插件入口文件
require_once('../wp-content/plugins/nextgen-gallery/ngg-config.php');
require_once('../inc/php/cfg.php');

$fotos=nggdb::find_last_images(0,10000);

$head="<?xml version='1.0' encoding='UTF-8'?>
	<urlset xmlns=\"http://www.google.com/schemas/sitemap/0.84\"
	xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\"
	xsi:schemaLocation=\"http://www.google.com/schemas/sitemap/0.84
	http://www.google.com/schemas/sitemap/0.84/sitemap.xsd\">";
echo $head;
foreach($fotos as $foto){
	$url = '<url>';
	$url.= '<loc>'.$cfg['siteurl'].'foto/'.$foto->pid.'.html</loc>';
	if(!empty($foto->imagedate)){
		$url.= '<lastmod>'.substr($foto->imagedate,0,10).'</lastmod>';
	}
	$url.= '<changefreq>daily</changefreq>';
	$url.= '<priority>';
	if(empty($foto->description)){
		$url.= '0.4';
	}else{
		$url.= '0.5';
	}
	$url.= '</priority>';
	$url.= '</url>';
	echo $url;
}

echo $foot="</urlset>";
//$all=$head.$url.$foot;
die();
/*
function wlog($filename, $msg){
		$handle = fopen($filename, 'w');
		fwrite($handle, $msg);
		fclose($handle);
}
if(wlog('./foto50k.xml',$all)) {
	echo 'good';
}else{
	echo 'bad';
}
*/
?>