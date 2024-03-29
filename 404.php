<?php
require_once('./wp-content/plugins/nextgen-gallery/ngg-config.php');
require_once('./inc/php/cfg.php');

$fotos=nggdb::get_random_images();
$foto=$fotos[0];
$tags=wp_get_object_terms($_GET['pid'],'ngg_tag');

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
$page_title='HTTP 404 not found';
$title=$page_title.$cfg['sitetitle'];

require_once './inc/html/head.html';
?>

<div id='main'>
<script type="text/javascript">
  var GOOG_FIXURL_LANG = 'en';
  var GOOG_FIXURL_SITE = 'http://foto.tfbj.cc/';
</script>
<script type="text/javascript" 
    src="http://linkhelp.clients.google.com/tbproxy/lh/wm/fixurl.js"></script>
  <p><img title="<?php echo $foto->alttext; ?>" src="<?php echo $foto_url; ?>" alt="<?php echo $foto->alttext; ?>"></p>
  <a href="/foto/<?php echo $foto->pid - 1; ?>.html">« Previous</a> <a href="/foto.php">Random</a> <a href="/foto/<?php echo $foto->pid + 1; ?>.html">Next »</a>
  <div class="google">
    <script type="text/javascript"><!--
	google_ad_client = "pub-6834157029902877";
	/* 728x90, 创建于 08-12-23 */
	google_ad_slot = "0543933874";
	google_ad_width = 728;
	google_ad_height = 90;
	//-->
	</script>
    <script type="text/javascript"
	src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
	</script>
  </div>
  <p>Tags:
    <?php if (!empty($tags)) { foreach($tags as $tag){ ?>
    <a href="/tag/<?php echo $tag->term_id; ?>-<?php echo $tag->slug; ?>.html"><?php echo $tag->slug; ?></a>
    <?php } } else{ echo 'none'; } ?>
  </p>
  <div id='friend'>
    <!-- Include the Google Friend Connect javascript library. -->
    <script type="text/javascript" src="http://www.google.com/friendconnect/script/friendconnect.js"></script>
    <!-- Define the div tag where the gadget will be inserted. -->
    <div id="div-1230889880420" style="width:600px;border:1px solid #cccccc;"></div>
    <!-- Render the gadget into a div. -->
    <script type="text/javascript">
var skin = {};
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
skin['DEFAULT_COMMENT_TEXT'] = 'RATE FOTO?';
skin['HEADER_TEXT'] = 'Ratings';
skin['POSTS_PER_PAGE'] = '5';
google.friendconnect.container.setParentUrl('/' /* location of rpc_relay.html and canvas.html */);
google.friendconnect.container.renderReviewGadget(
 { id: 'div-1230889880420',
   site: '10631887459699564576',
   'view-params':{"disableMinMax":"false","scope":"PAGE","allowAnonymousPost":"true","startMaximized":"true"}
},
  skin);
</script>
  </div>
</div>
<?php 
require_once './inc/html/foot.html';?>