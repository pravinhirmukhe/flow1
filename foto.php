<?php
require_once('./wp-content/plugins/nextgen-gallery/ngg-config.php');
require_once('./inc/php/cfg.php');

if(!empty($_GET['pid'])){
	$foto=nggdb::find_image($_GET['pid']);
	if(empty($foto) or $foto->exclude == 1){
		$fotos=nggdb::get_random_images();
		$foto=$fotos[0];
	}
	$tags=wp_get_object_terms($_GET['pid'],'ngg_tag');
}else{
	if($_SESSION['last_pid'] > 0 ){
		$pid=mt_rand(1,$_SESSION['last_pid']);
		header("Location: foto.php?pid=$pid");
	}else{
		header('Location: /');
	}
}

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
$page_title=empty($foto->alttext)?'FOTO':$foto->alttext;
$title=$page_title.$cfg['sitetitle'];

require_once './inc/html/head.html';
?>
<div id='main'>
	</div>
  <p> <a href="foto.php" title="rand foto"> <img title="<?php echo $foto->alttext; ?>" src="<?php echo $foto_url; ?>" alt="<?php echo $foto->alttext; ?>"> </a></p>
  <a href="foto.php?pid=<?php echo $foto->pid - 1; ?>">« Previous</a> <a href="foto.php">Random</a> <a href="foto.php?pid=<?php echo $foto->pid + 1; ?>">Next »</a>
  <p>Tags:
  	<?php if (!empty($tags)) { foreach($tags as $tag){ ?>
	    <a href="tag.php?tagid=<?php echo $tag->term_id; ?>&tag=<?php echo $tag->slug; ?>"><?php echo $tag->slug; ?></a>
		<?php } } else{ echo 'none'; } ?>
  </p>
  
<div id='friend'><!-- Include the Google Friend Connect javascript library. -->
<script type="text/javascript" src="http://www.google.com/friendconnect/script/friendconnect.js"></script>

<!-- Define the div tag where the gadget will be inserted. -->
<div id="div-1230468400576" style="width:600px;border:1px solid #cccccc;"></div>
<!-- Render the gadget into a div. -->
<script type="text/javascript">
var skin = {};
skin['HEIGHT'] = '180';
skin['BORDER_COLOR'] = '#cccccc';
skin['ENDCAP_BG_COLOR'] = '#e6e6e6';
skin['ENDCAP_TEXT_COLOR'] = '#333333';
skin['ENDCAP_LINK_COLOR'] = '#666666';
skin['ALTERNATE_BG_COLOR'] = '#ffffff';
skin['CONTENT_BG_COLOR'] = '#ffffff';
skin['CONTENT_LINK_COLOR'] = '#0000cc';
skin['CONTENT_TEXT_COLOR'] = '#333333';
skin['CONTENT_SECONDARY_LINK_COLOR'] = '#7777cc';
skin['CONTENT_SECONDARY_TEXT_COLOR'] = '#666666';
skin['CONTENT_HEADLINE_COLOR'] = '#333333';
google.friendconnect.container.setParentUrl('/' /* location of rpc_relay.html and canvas.html */);
google.friendconnect.container.renderMembersGadget(
 { id: 'div-1230468400576',
   site: '10631887459699564576'},
  skin);
</script>
</div>
	<div class="google"><script type="text/javascript"><!--
google_ad_client = "pub-6834157029902877";
/* 728x90, 创建于 08-12-23 */
google_ad_slot = "0543933874";
google_ad_width = 728;
google_ad_height = 90;
//-->
</script>
<script type="text/javascript"
src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
</script></div>
</div>
<?php 
require_once './inc/html/foot.html';?>