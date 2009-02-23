<?php
session_start();
$cfg['siteurl']='http://foto.tfbj.cc/';
$cfg['baseurl']='http://stat001.tfbj.cc/';
$cfg['trueurl']='http://foto.tfbj.cc/wp-content/gallery/';
$cfg['sitename']='FOTO';
$cfg['sitetitle']=" | foto my life | foto your life | foto everyone's life";
$cfg['description']='To find all beautiful things | colorful world,young body,pure desire,real dream,crazy movement';
$cfg['keywords']='photo,graphic,shot,polaroid,camera,perfect,gorgeous,picture,nice,sex,babes';
$canonical = canonical();

//处理图片url 隐藏真实地址
function fotourl($url){
	global $cfg;
	return str_replace($cfg['trueurl'],$cfg['baseurl'],$url);
}

//生成canonical标签内容
function canonical($id=0,$slug=''){
	global $cfg;
	switch($_SERVER['SCRIPT_NAME']){
		case '/foto.php':
			return $cfg['siteurl'].'foto/'.$id.'.html';
		break;

		case '/tag.php':
			return $cfg['siteurl'].'tag/'.$id.'-'.$slug.'.html';
		break;

		default:
			return $cfg['siteurl'].substr($_SERVER['SCRIPT_NAME'],1);
	}
}
?>
