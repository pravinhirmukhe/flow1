<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><head profile="http://gmpg.org/xfn/11">  

<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">    
<title>tags | FOTO!</title>
<meta name="generator" content="WordPress 2.6.5">
<link rel="stylesheet" href="tags%20_%20FOTO%21_files/style.css" type="text/css" media="screen" title="black">  
<meta name="generator" content="WordPress 2.6.5">
 
</head><body>
<div class="container">
<h1><a href="http://foto.tfbg.net/">FOTO!</a></h1>
<div class="nav">
<a href="about.php" title="about">about</a> · <a title="tags" href="http://foto.iysh.net/tags">tags</a>
</div>
<div class="pages tagslist">
<?php
require_once './inc/php/object.php';
require_once './inc/php/dbs.php';
$db = DBS::init();
//-------------------------

if($_SERVER['REQUEST_METHOD']=='GET'){
	$sql="select t.*, tr.name from ft_term_taxonomy as t left join ft_terms as tr on tr.term_id=t.term_id order by t.term_id desc";
	//$sqls="select tagid , count(*) as tagsize from ft_term_relationships group by tagid order by tagid";
	//$size="select count(*) as size from ft_ngg_pic2tags order by size";
	//var_dump($sql);
	$result = $db -> query($sql);
	//$results = $db -> query($sqls);
	//$result_s = $db -> query($size);

	//$size = mysql_num_rows($result_s);
	//20段，每段的tag数
	//$size = $size/20;

	//if(!empty($result) and !empty($results)){
	if(!empty($result)){
		//$num = mysql_num_rows($result);
		$i = 100;
		if($num > $i){
			while ($i > 0) {
				$re = $result -> fetch_assoc();
				//$res = $results -> fetch_assoc();
				//$tagsize = $res['tagsize'];
				//$tagsize = 
				echo "<font size='{}'><a href='picture.php?tag={$re['term_id']}&num=1'>".$re['name']."</a></font>&nbsp;&nbsp;";
				$i--;
			}
		}else{
			while ($re = $result -> fetch_assoc()) {
				//$res = $results -> fetch_assoc();
				echo "<font size='{}'><a href='picture.php?tag={$re['term_id']}&num=1'>".$re['name']."</a></font>&nbsp;&nbsp;";
			}
		}
	}else{
		echo "<font color='red'>no data!</font>";
	}
}

DBS::end();
?>


</div><!--end pages tagslist-->
<div class="footer">
© Copyright FOTO!  ·  All rights reserved  ·  
</div><span style="display: none;">
</body></html>