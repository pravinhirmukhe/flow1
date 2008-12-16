<?php
require_once './inc/php/object.php';
require_once './inc/php/error.php';
require_once './inc/php/dbs.php';
try{
	$db = DBS::init();
	$home = "http://foto.tfbj.cc";
	$url="http://stat001.tfbj.cc/**/";

	$glry = "ft_ngg_gallery";
	$pic  = "ft_ngg_pictures";
	$tags = "ft_terms";
	$p2t  = "ft_term_relationships";
	
	$pid = isset($_GET['pid'])?$_GET['pid']:0;
	$tid = isset($_GET['tag'])?$_GET['tag']:0;
	$num = isset($_GET['num'])?$_GET['num']:0;
	$mext= $num+1;
	
	if($pid>0){
		$begin = $pid-1;
		$sql="SELECT gry.path as path, pic.pid as id, pic.alttext as alt, pic.filename 
	      FROM  $glry as gry, $pic as pic
		  where pic.galleryid=gry.gid 
		  limit {$begin},1  ";
		  $rt=$db->query($sql);
		  if(!$rt){
			header("locaton: index.php");
		  }else{
			$obj= $rt->fetch_object();
			if(is_object($obj)){
			}else{
				throw new error("error in dataset");
			}
		  }
		  $tsql="select tag.term_id as tid, tag.name as tags 
				 from $tags as tag, $p2t as p2t
				 where object_id=$obj->id and p2t.term_taxonomy_id=tag.term_id ";
		  $rtt=$db->query($tsql);
		  if($rtt){
			  $tobj= $rtt->fetch_object();
		  }

		  $tag=$tobj->tags;
		  $tid = $tobj->tid;
		  $start = strrpos($obj->path, '/');
		  $dir = substr($obj->path, $start+1);
		  $alt = $obj->alt;  
		  $next  = $pid-1;
		  $cimg= str_replace('**', $dir, $url).$obj->filename;  # current imgae
		  $previmg= "picture.php?pid={$next}";			  # next image
		  $tagimg = "picture.php?tag={$tid}&num={$mext}";	  # image of image 


	}else{
		$begin = $num-1;
		$sql="SELECT gry.path as path, tags.name as tag, pic.pid as id, pic.alttext as alt, pic.filename , tags.term_id as tid
	      FROM  $glry as gry, $pic as pic, $tags as tags, $p2t as p2t 
		  where pic.galleryid=gry.gid and pic.pid=p2t.object_id and p2t.term_taxonomy_id=tags.term_id and tags.term_id={$tid}
		  order by pic.pid desc 
		  limit {$begin}, 1 ";		

		  $rt=$db->query($sql);
		  if(!empty($rt)){
		  }else{
			$obj= $rt->fetch_object();
			if(empty($obj)){
				header("locaton: index.php");
			}else{
			}
		  }
		  $start = strrpos($obj->path, '/');
		  $dir = substr($obj->path, $start+1);
		  $alt = $obj->alt;  
		  $next  = $obj->pid-1;
		  $cimg= str_replace('**', $dir, $url).$obj->filename;  # current imgae
		  $tagimg = "picture.php?tag={$tid}&num={$mext}";	  # image of image 
		  $previmg=$tagimg;
		  $tag=$obj->tags;
		  $tid = $obj->tid;
	}	
	
	DBS::end();
}catch(error $e){
	$msg= $e->getMessage();
	echo $msg;
	
}
require_once './inc/html/head.html';
?>
<div id='main'>
  <p> <a href="<?php echo $previmg; ?>" title="Previous"> <img class="alignnone" title="<?php echo $ititle; ?>" src="<?php echo $cimg; ?>" alt="<?php echo $alt; ?>"> </a></p>
  <p><a href="<?php echo $previmg;?>">« Previous</a></p>
  <p>Tags:<a href="<?php echo $tagimg; ?>" title="<?php echo $tag; ?>"><?php echo $tag; ?></a></p>
</div>
<?php 
require_once './inc/html/foot.html';?>