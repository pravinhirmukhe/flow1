<?php
/**
 * Atom Feed Template for displaying Atom Comments feed.
 *
 * @package WordPress
 */

header('Content-Type: application/atom+xml; charset=' . get_option('blog_charset'), true);
echo '<?xml version="1.0" encoding="' . get_option('blog_charset') . '" ?' . '>';
?>
<feed
	xmlns="http://www.w3.org/2005/Atom"
	xml:lang="<?php echo get_option('rss_language'); ?>"
	<?php do_action('atom_ns'); ?>
>
	<title type="text"><?php
		if ( is_singular() )
			printf(ent2ncr(__('Comments on: %s')), get_the_title_rss());
		elseif ( is_search() )
			printf(ent2ncr(__('Comments for %1$s searching on %2$s')), get_bloginfo_rss( 'name' ), attribute_escape(get_search_query()));
		else
			printf(ent2ncr(__('Comments for %s')), get_bloginfo_rss( 'name' ) . get_wp_title_rss());
	?></title>
	<subtitle type="text"><?php bloginfo_rss('description'); ?></subtitle>

	<updated><?php echo mysql2date('Y-m-d\TH:i:s\Z', get_lastcommentmodified('GMT')); ?></updated>
	<?php the_generator( 'atom' ); ?>

<?php if ( is_singular() ) { ?>
	<link rel="alternate" type="<?php bloginfo_rss('html_type'); ?>" href="<?php echo get_comments_link(); ?>" />
	<link rel="self" type="application/atom+xml" href="<?php echo get_post_comments_feed_link('', 'atom'); ?>" />
	<id><?php echo get_post_comments_feed_link('', 'atom'); ?></id>
<?php } elseif(is_search()) { ?>
	<link rel="alternate" type="<?php bloginfo_rss('html_type'); ?>" href="<?php echo get_option('home') . '?s=' . attribute_escape(get_search_query()); ?>" />
	<link rel="self" type="application/atom+xml" href="<?php echo get_search_comments_feed_link('', 'atom'); ?>" />
	<id><?php echo get_search_comments_feed_link('', 'atom'); ?></id>
<?php } else { ?>
	<link rel="alternate" type="<?php bloginfo_rss('html_type'); ?>" href="<?php bloginfo_rss('home'); ?>" />
	<link rel="self" type="application/atom+xml" href="<?php bloginfo_rss('comments_atom_url'); ?>" />
	<id><?php bloginfo_rss('comments_atom_url'); ?></id>
<?php } ?>

<?php
if ( have_comments() ) : while ( have_comments() ) : the_comment();
	$comment_post = get_post($comment->comment_post_ID);
	get_post_custom($comment_post->ID);
?>
	<entry>
		<title><?php
			if ( !is_singular() ) {
				$title = get_the_title($comment_post->ID);
				$title = apply_filters('the_title_rss', $title);
				printf(ent2ncr(__('Comment on %1$s by %2$s')), $title, get_comment_author_rss());
			} else {
				printf(ent2ncr(__('By: %s')), get_comment_author_rss());
			}
		?></title>
		<link rel="alternate" href="<?php comment_link(); ?>" type="<?php bloginfo_rss('html_type'); ?>" />

		<author>
			<name><?php comment_author_rss(); ?></name>
			<?php if (get_comment_author_url()) echo '<uri>' . get_comment_author_url() . '</uri>'; ?>

		</author>

		<id><?php comment_link(); ?></id>
		<updated><?php echo mysql2date('Y-m-d\TH:i:s\Z', get_comment_time('Y-m-d H:i:s', true), false); ?></updated>
		<published><?php echo mysql2date('Y-m-d\TH:i:s\Z', get_comment_time('Y-m-d H:i:s', true), false); ?></published>
<?php if ( post_password_required($comment_post) ) : ?>
		<content type="html" xml:base="<?php comment_link(); ?>"><![CDATA[<?php echo get_the_password_form(); ?>]]></content>
<?php else : // post pass ?>
		<content type="html" xml:base="<?php comment_link(); ?>"><![CDATA[<?php comment_text(); ?>]]></content>
<?php endif; // post pass
	// Return comment threading information (http://www.ietf.org/rfc/rfc4685.txt)
	if ( $comment->comment_parent == 0 ) : // This comment is top level ?>
		<thr:in-reply-to ref="<?php the_guid() ?>" href="<?php the_permalink_rss() ?>" type="<?php bloginfo_rss('html_type'); ?>" />
<?php else : // This comment is in reply to another comment
	$parent_comment = get_comment($comment->comment_parent);
	// The rel attribute below and the id tag above should be GUIDs, but WP doesn't create them for comments (unlike posts). Either way, its more important that they both use the same system
?>
		<thr:in-reply-to ref="<?php echo get_comment_link($parent_comment) ?>" href="<?php echo get_comment_link($parent_comment) ?>" type="<?php bloginfo_rss('html_type'); ?>" />
<?php endif;
	do_action('comment_atom_entry', $comment->comment_ID, $comment_post->ID);
?>
	</entry>
<?php endwhile; endif; ?>
</feed>
