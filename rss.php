<?php
//引入图片插件入口文件
require_once('./wp-content/plugins/nextgen-gallery/ngg-config.php');
require_once('./inc/php/cfg.php');

$fotos=nggdb::find_last_images(0,300);
//var_dump($fotos);
header('Content-Type: text/xml; charset=' . get_option('blog_charset'), true);
?>
<?php echo '<?xml version="1.0" encoding="'.get_option('blog_charset').'"?'.'>'; ?>

<rss version="2.0"
	xmlns:content="http://purl.org/rss/1.0/modules/content/"
	xmlns:wfw="http://wellformedweb.org/CommentAPI/"
	xmlns:dc="http://purl.org/dc/elements/1.1/"
	xmlns:atom="http://www.w3.org/2005/Atom"
	xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
>

<channel>
	<title><?php bloginfo_rss('name'); wp_title_rss(); ?></title>
	<link><?php bloginfo_rss('url') ?></link>
	<description><?php bloginfo_rss("description") ?></description>
	<pubDate><?php echo mysql2date('D, d M Y H:i:s +0000', $fotos[0]->imagedate, false); ?></pubDate>	<generator>http://www.tfbj.cc/</generator>
	<language><?php echo get_option('rss_language'); ?></language>
	<sy:updatePeriod><?php echo apply_filters( 'rss_update_period', 'hourly' ); ?></sy:updatePeriod>
	<sy:updateFrequency><?php echo apply_filters( 'rss_update_frequency', '1' ); ?></sy:updateFrequency>
	<?php do_action('rss2_head'); ?>
	<?php if(!empty($fotos)){ foreach($fotos as $foto){ ?>
	<item>
		<title><?php echo $foto->alttext; ?></title>
		<link><?php echo $cfg['siteurl'].'foto.php?pid='.$foto->pid; ?></link>
		<comments><?php comments_link(); ?></comments>
		<pubDate><?php echo mysql2date('D, d M Y H:i:s +0000', $foto->imagedate, false); ?></pubDate>
		<dc:creator><?php echo $foto->title ?></dc:creator>
		<guid isPermaLink="false"><?php echo $cfg['siteurl'].'foto.php?pid='.$foto->pid; ?></guid>
		<description><![CDATA[<a href="<?php echo $cfg['siteurl'].'foto.php?pid='.$foto->pid; ?>"><img border="0" alt="<?php echo $foto->alttext; ?>" src="<?php echo fotourl($foto->thumbURL); ?>"></a>"]]></description>
		<content:encoded><![CDATA[<a href="<?php echo $cfg['siteurl'].'foto.php?pid='.$foto->pid; ?>"><img border="0" alt="<?php echo $foto->alttext; ?>" src="<?php echo fotourl($foto->thumbURL); ?>"></a>]]></content:encoded>
		<enclosure url="<?php echo fotourl($foto->imageURL); ?>" type="image/jpeg" />
	<?php do_action('rss2_item'); ?>
	</item>
	<?php } } ?>
</channel>
</rss>