<?php
//引入图片插件入口文件
require_once('./wp-content/plugins/nextgen-gallery/ngg-config.php');
require_once('./inc/php/cfg.php');


$args = array('orderby' => 'term_id', 'order' => 'DESC',
		'hide_empty' => true, 'exclude' => '', 'include' => '',
		'number' => '400', 'fields' => 'all', 'slug' => '', 'parent' => '',
		'hierarchical' => true, 'child_of' => 0, 'get' => '', 'name__like' => '',
		'pad_counts' => false, 'offset' => '', 'search' => '');

$tags=get_terms('ngg_tag',$args);
echo '<?xml version="1.0" encoding="'.get_option('blog_charset').'"?'.">\n"; 
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<?php if(!empty($tags)){ foreach($tags as $tag){ ?>
	<url>
		<loc><?php echo $cfg['siteurl'].'tag/'.$tag->term_id.'-'.$tag->slug.'.html'; ?></loc>
		<changefreq>daily</changefreq>
		<priority><?php echo abs(round( ( ($tag->count - 1) / $tag->count) - 0.1,1)) ; ?></priority>
	</url> 
<?php } } ?>
</urlset>