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
//-------------------------字体大小函数
    function showTag($cur,$tag,$id){
        $maxSize=1300;$minSize=30;		//maxSize为标签字体最大大小,minSize为标签最小大小 按百分比
        $max=100;$min=8;			//max为最多标签点击数 min为最少点击数 cur为当前标签当前点击数
        $tagColor=$tagSize=$minSize;			#tagColor为标签最终颜色，tagsize为标签最终字体大小
        $tagSize=@(float)($cur-$min)/($max-$min)*($maxSize-$minSize);
    
        $colors=array("0.2"=>"#333333","0.4"=>"#0033CC","0.6"=>"#660099","0.8"=>"#00FFFF","1"=>"#FF0000");		//color为存放颜色的数组，按百分百存放，例如在0-0.2之间的color1
        $colorkeys=array_keys($colors);
        
        $select=(float)($cur-$min)/($max-$min);
        $len=count($colorkeys);
        $i=0;$j=$len-1;
        while($i<$j){		//二分查找
            $index=($i+$j)/2;
            if($select>=(float)$colorkeys[$index] && $select<(float)$colorkeys[$index+1]) break;
            elseif($select<(float)$colorkeys[$index]) $j--;
            elseif($select>(float)$colorkeys[$index]) $i++;
            elseif($select==(float)$colorkeys[$index+1]) break;
            else break;
        }
        $tagColor=$colors[$colorkeys{($i+$j)/2}];
        echo "<span style='font-size:{$tagSize}%;color:{$tagColor}'><a href='picture.php?tag={$id}&num=1'>{$tag}</a></span>&nbsp;&nbsp;";
    }
//-------------------------函数结束

if($_SERVER['REQUEST_METHOD']=='GET'){
	$sql="select t.*, tr.name from ft_term_taxonomy as t left join ft_terms as tr on tr.term_id=t.term_id order by t.term_id desc";

	$result = $db -> query($sql);
	if(!empty($result)){
		$sqls = "select count(*) as num from ft_terms";
		$results = $db -> query($sqls);
		$nums = $results -> fetch_assoc();
		$num = $nums['num'];
		$i = 100;
		if($num > $i){
			while ($i > 0) {
				$re = $result -> fetch_assoc();
				showTag($re['count'],$re['name'],$re['term_id']);
				$i--;
			}
		}else{
			$tags = array();
			while ($re = $result -> fetch_assoc()) {
				showTag($re['count'],$re['name'],$re['term_id']);
			}
		}
	}else{
		echo "<font color='red'>no data!</font>";
	}
}

DBS::end();
?>
</div>
<?php
require_once './inc/html/foot.html';?>