<?php
/**
 * WordPress Link Template Functions
 *
 * @package WordPress
 * @subpackage Template
 */

/**
 * Display the permalink for the current post.
 *
 * @since 1.2.0
 * @uses apply_filters() Calls 'the_permalink' filter on the permalink string.
 */
function the_permalink() {
	echo apply_filters('the_permalink', get_permalink());
}

/**
 * Retrieve trailing slash string, if blog set for adding trailing slashes.
 *
 * Conditionally adds a trailing slash if the permalink structure has a trailing
 * slash, strips the trailing slash if not. The string is passed through the
 * 'user_trailingslashit' filter. Will remove trailing slash from string, if
 * blog is not set to have them.
 *
 * @since 2.2.0
 * @uses $wp_rewrite
 *
 * @param $string String a URL with or without a trailing slash.
 * @param $type_of_url String the type of URL being considered (e.g. single, category, etc) for use in the filter.
 * @return string
 */
function user_trailingslashit($string, $type_of_url = '') {
	global $wp_rewrite;
	if ( $wp_rewrite->use_trailing_slashes )
		$string = trailingslashit($string);
	else
		$string = untrailingslashit($string);

	// Note that $type_of_url can be one of following:
	// single, single_trackback, single_feed, single_paged, feed, category, page, year, month, day, paged
	$string = apply_filters('user_trailingslashit', $string, $type_of_url);
	return $string;
}

/**
 * Display permalink anchor for current post.
 *
 * The permalink mode title will use the post title for the 'a' element 'id'
 * attribute. The id mode uses 'post-' with the post ID for the 'id' attribute.
 *
 * @since 0.71
 *
 * @param string $mode Permalink mode can be either 'title', 'id', or default, which is 'id'.
 */
function permalink_anchor($mode = 'id') {
	global $post;
	switch ( strtolower($mode) ) {
		case 'title':
			$title = sanitize_title($post->post_title) . '-' . $post->ID;
			echo '<a id="'.$title.'"></a>';
			break;
		case 'id':
		default:
			echo '<a id="post-' . $post->ID . '"></a>';
			break;
	}
}

/**
 * Retrieve full permalink for current post or post ID.
 *
 * @since 1.0.0
 *
 * @param int $id Optional. Post ID.
 * @param bool $leavename Optional, defaults to false. Whether to keep post name or page name.
 * @return string
 */
function get_permalink($id = 0, $leavename = false) {
	$rewritecode = array(
		'%year%',
		'%monthnum%',
		'%day%',
		'%hour%',
		'%minute%',
		'%second%',
		$leavename? '' : '%postname%',
		'%post_id%',
		'%category%',
		'%author%',
		$leavename? '' : '%pagename%',
	);

	$post = &get_post($id);

	if ( empty($post->ID) ) return false;

	if ( $post->post_type == 'page' )
		return get_page_link($post->ID, $leavename);
	elseif ($post->post_type == 'attachment')
		return get_attachment_link($post->ID);

	$permalink = get_option('permalink_structure');

	if ( '' != $permalink && !in_array($post->post_status, array('draft', 'pending')) ) {
		$unixtime = strtotime($post->post_date);

		$category = '';
		if ( strpos($permalink, '%category%') !== false ) {
			$cats = get_the_category($post->ID);
			if ( $cats ) {
				usort($cats, '_usort_terms_by_ID'); // order by ID
				$category = $cats[0]->slug;
				if ( $parent = $cats[0]->parent )
					$category = get_category_parents($parent, false, '/', true) . $category;
			}
			// show default category in permalinks, without
			// having to assign it explicitly
			if ( empty($category) ) {
				$default_category = get_category( get_option( 'default_category' ) );
				$category = is_wp_error( $default_category ) ? '' : $default_category->slug;
			}
		}

		$author = '';
		if ( strpos($permalink, '%author%') !== false ) {
			$authordata = get_userdata($post->post_author);
			$author = $authordata->user_nicename;
		}

		$date = explode(" ",date('Y m d H i s', $unixtime));
		$rewritereplace =
		array(
			$date[0],
			$date[1],
			$date[2],
			$date[3],
			$date[4],
			$date[5],
			$post->post_name,
			$post->ID,
			$category,
			$author,
			$post->post_name,
		);
		$permalink = get_option('home') . str_replace($rewritecode, $rewritereplace, $permalink);
		$permalink = user_trailingslashit($permalink, 'single');
		return apply_filters('post_link', $permalink, $post, $leavename);
	} else { // if they're not using the fancy permalink option
		$permalink = get_option('home') . '/?p=' . $post->ID;
		return apply_filters('post_link', $permalink, $post, $leavename);
	}
}

/**
 * Retrieve permalink from post ID.
 *
 * @since 1.0.0
 *
 * @param int $post_id Optional. Post ID.
 * @param mixed $deprecated Not used.
 * @return string
 */
function post_permalink($post_id = 0, $deprecated = '') {
	return get_permalink($post_id);
}

/**
 * Retrieve the permalink for current page or page ID.
 *
 * Respects page_on_front. Use this one.
 *
 * @since 1.5.0
 *
 * @param int $id Optional. Post ID.
 * @param bool $leavename Optional, defaults to false. Whether to keep post name or page name.
 * @return string
 */
function get_page_link($id = false, $leavename = false) {
	global $post;

	$id = (int) $id;
	if ( !$id )
		$id = (int) $post->ID;

	if ( 'page' == get_option('show_on_front') && $id == get_option('page_on_front') )
		$link = get_option('home');
	else
		$link = _get_page_link( $id , $leavename );

	return apply_filters('page_link', $link, $id);
}

/**
 * Retrieve the page permalink.
 *
 * Ignores page_on_front. Internal use only.
 *
 * @since 2.1.0
 * @access private
 *
 * @param int $id Optional. Post ID.
 * @param bool $leavename Optional. Leave name.
 * @return string
 */
function _get_page_link( $id = false, $leavename = false ) {
	global $post, $wp_rewrite;

	if ( !$id )
		$id = (int) $post->ID;
	else
		$post = &get_post($id);

	$pagestruct = $wp_rewrite->get_page_permastruct();

	if ( '' != $pagestruct && isset($post->post_status) && 'draft' != $post->post_status ) {
		$link = get_page_uri($id);
		$link = ( $leavename ) ? $pagestruct : str_replace('%pagename%', $link, $pagestruct);
		$link = get_option('home') . "/$link";
		$link = user_trailingslashit($link, 'page');
	} else {
		$link = get_option('home') . "/?page_id=$id";
	}

	return apply_filters( '_get_page_link', $link, $id );
}

/**
 * Retrieve permalink for attachment.
 *
 * This can be used in the WordPress Loop or outside of it.
 *
 * @since 2.0.0
 *
 * @param int $id Optional. Post ID.
 * @return string
 */
function get_attachment_link($id = false) {
	global $post, $wp_rewrite;

	$link = false;

	if (! $id) {
		$id = (int) $post->ID;
	}

	$object = get_post($id);
	if ( $wp_rewrite->using_permalinks() && ($object->post_parent > 0) && ($object->post_parent != $id) ) {
		$parent = get_post($object->post_parent);
		if ( 'page' == $parent->post_type )
			$parentlink = _get_page_link( $object->post_parent ); // Ignores page_on_front
		else
			$parentlink = get_permalink( $object->post_parent );
		if ( is_numeric($object->post_name) || false !== strpos(get_option('permalink_structure'), '%category%') )
			$name = 'attachment/' . $object->post_name; // <permalink>/<int>/ is paged so we use the explicit attachment marker
		else
			$name = $object->post_name;
		if (strpos($parentlink, '?') === false)
			$link = user_trailingslashit( trailingslashit($parentlink) . $name );
	}

	if (! $link ) {
		$link = get_bloginfo('url') . "/?attachment_id=$id";
	}

	return apply_filters('attachment_link', $link, $id);
}

/**
 * Retrieve the permalink for the year archives.
 *
 * @since 1.5.0
 *
 * @param int|bool $year False for current year or year for permalink.
 * @return string
 */
function get_year_link($year) {
	global $wp_rewrite;
	if ( !$year )
		$year = gmdate('Y', time()+(get_option('gmt_offset') * 3600));
	$yearlink = $wp_rewrite->get_year_permastruct();
	if ( !empty($yearlink) ) {
		$yearlink = str_replace('%year%', $year, $yearlink);
		return apply_filters('year_link', get_option('home') . user_trailingslashit($yearlink, 'year'), $year);
	} else {
		return apply_filters('year_link', get_option('home') . '/?m=' . $year, $year);
	}
}

/**
 * Retrieve the permalink for the month archives with year.
 *
 * @since 1.0.0
 *
 * @param bool|int $year False for current year. Integer of year.
 * @param bool|int $month False for current month. Integer of month.
 * @return string
 */
function get_month_link($year, $month) {
	global $wp_rewrite;
	if ( !$year )
		$year = gmdate('Y', time()+(get_option('gmt_offset') * 3600));
	if ( !$month )
		$month = gmdate('m', time()+(get_option('gmt_offset') * 3600));
	$monthlink = $wp_rewrite->get_month_permastruct();
	if ( !empty($monthlink) ) {
		$monthlink = str_replace('%year%', $year, $monthlink);
		$monthlink = str_replace('%monthnum%', zeroise(intval($month), 2), $monthlink);
		return apply_filters('month_link', get_option('home') . user_trailingslashit($monthlink, 'month'), $year, $month);
	} else {
		return apply_filters('month_link', get_option('home') . '/?m=' . $year . zeroise($month, 2), $year, $month);
	}
}

/**
 * Retrieve the permalink for the day archives with year and month.
 *
 * @since 1.0.0
 *
 * @param bool|int $year False for current year. Integer of year.
 * @param bool|int $month False for current month. Integer of month.
 * @param bool|int $day False for current day. Integer of day.
 * @return string
 */
function get_day_link($year, $month, $day) {
	global $wp_rewrite;
	if ( !$year )
		$year = gmdate('Y', time()+(get_option('gmt_offset') * 3600));
	if ( !$month )
		$month = gmdate('m', time()+(get_option('gmt_offset') * 3600));
	if ( !$day )
		$day = gmdate('j', time()+(get_option('gmt_offset') * 3600));

	$daylink = $wp_rewrite->get_day_permastruct();
	if ( !empty($daylink) ) {
		$daylink = str_replace('%year%', $year, $daylink);
		$daylink = str_replace('%monthnum%', zeroise(intval($month), 2), $daylink);
		$daylink = str_replace('%day%', zeroise(intval($day), 2), $daylink);
		return apply_filters('day_link', get_option('home') . user_trailingslashit($daylink, 'day'), $year, $month, $day);
	} else {
		return apply_filters('day_link', get_option('home') . '/?m=' . $year . zeroise($month, 2) . zeroise($day, 2), $year, $month, $day);
	}
}

/**
 * Retrieve the permalink for the feed type.
 *
 * @since 1.5.0
 *
 * @param string $feed Optional, defaults to default feed. Feed type.
 * @return string
 */
function get_feed_link($feed = '') {
	global $wp_rewrite;

	$permalink = $wp_rewrite->get_feed_permastruct();
	if ( '' != $permalink ) {
		if ( false !== strpos($feed, 'comments_') ) {
			$feed = str_replace('comments_', '', $feed);
			$permalink = $wp_rewrite->get_comment_feed_permastruct();
		}

		if ( get_default_feed() == $feed )
			$feed = '';

		$permalink = str_replace('%feed%', $feed, $permalink);
		$permalink = preg_replace('#/+#', '/', "/$permalink");
		$output =  get_option('home') . user_trailingslashit($permalink, 'feed');
	} else {
		if ( empty($feed) )
			$feed = get_default_feed();

		if ( false !== strpos($feed, 'comments_') )
			$feed = str_replace('comments_', 'comments-', $feed);

		$output = get_option('home') . "/?feed={$feed}";
	}

	return apply_filters('feed_link', $output, $feed);
}

/**
 * Retrieve the permalink for the post comments feed.
 *
 * @since 2.2.0
 *
 * @param int $post_id Optional. Post ID.
 * @param string $feed Optional. Feed type.
 * @return string
 */
function get_post_comments_feed_link($post_id = '', $feed = '') {
	global $id;

	if ( empty($post_id) )
		$post_id = (int) $id;

	if ( empty($feed) )
		$feed = get_default_feed();

	if ( '' != get_option('permalink_structure') ) {
		$url = trailingslashit( get_permalink($post_id) ) . 'feed';
		if ( $feed != get_default_feed() )
			$url .= "/$feed";
		$url = user_trailingslashit($url, 'single_feed');
	} else {
		$type = get_post_field('post_type', $post_id);
		if ( 'page' == $type )
			$url = get_option('home') . "/?feed=$feed&amp;page_id=$post_id";
		else
			$url = get_option('home') . "/?feed=$feed&amp;p=$post_id";
	}

	return apply_filters('post_comments_feed_link', $url);
}

/**
 * Display the comment feed link for a post.
 *
 * Prints out the comment feed link for a post. Link text is placed in the
 * anchor. If no link text is specified, default text is used. If no post ID is
 * specified, the current post is used.
 *
 * @package WordPress
 * @subpackage Feed
 * @since 2.5.0
 *
 * @param string $link_text Descriptive text.
 * @param int $post_id Optional post ID.  Default to current post.
 * @param string $feed Optional. Feed format.
 * @return string Link to the comment feed for the current post.
*/
function post_comments_feed_link( $link_text = '', $post_id = '', $feed = '' ) {
	$url = get_post_comments_feed_link($post_id, $feed);
	if ( empty($link_text) )
		$link_text = __('Comments Feed');

	echo "<a href='$url'>$link_text</a>";
}

/**
 * Retrieve the feed link for a given author.
 *
 * Returns a link to the feed for all posts by a given author. A specific feed
 * can be requested or left blank to get the default feed.
 *
 * @package WordPress
 * @subpackage Feed
 * @since 2.5.0
 *
 * @param int $author_id ID of an author.
 * @param string $feed Optional. Feed type.
 * @return string Link to the feed for the author specified by $author_id.
*/
function get_author_feed_link( $author_id, $feed = '' ) {
	$author_id = (int) $author_id;
	$permalink_structure = get_option('permalink_structure');

	if ( empty($feed) )
		$feed = get_default_feed();

	if ( '' == $permalink_structure ) {
		$link = get_option('home') . "?feed=$feed&amp;author=" . $author_id;
	} else {
		$link = get_author_posts_url($author_id);
		if ( $feed == get_default_feed() )
			$feed_link = 'feed';
		else
			$feed_link = "feed/$feed";

		$link = trailingslashit($link) . user_trailingslashit($feed_link, 'feed');
	}

	$link = apply_filters('author_feed_link', $link, $feed);

	return $link;
}

/**
 * Retrieve the feed link for a category.
 *
 * Returns a link to the feed for all post in a given category. A specific feed
 * can be requested or left blank to get the default feed.
 *
 * @package WordPress
 * @subpackage Feed
 * @since 2.5.0
 *
 * @param int $cat_id ID of a category.
 * @param string $feed Optional. Feed type.
 * @return string Link to the feed for the category specified by $cat_id.
*/
function get_category_feed_link($cat_id, $feed = '') {
	$cat_id = (int) $cat_id;

	$category = get_category($cat_id);

	if ( empty($category) || is_wp_error($category) )
		return false;

	if ( empty($feed) )
		$feed = get_default_feed();

	$permalink_structure = get_option('permalink_structure');

	if ( '' == $permalink_structure ) {
		$link = get_option('home') . "?feed=$feed&amp;cat=" . $cat_id;
	} else {
		$link = get_category_link($cat_id);
		if( $feed == get_default_feed() )
			$feed_link = 'feed';
		else
			$feed_link = "feed/$feed";

		$link = trailingslashit($link) . user_trailingslashit($feed_link, 'feed');
	}

	$link = apply_filters('category_feed_link', $link, $feed);

	return $link;
}

/**
 * Retrieve permalink for feed of tag.
 *
 * @since 2.3.0
 *
 * @param int $tag_id Tag ID.
 * @param string $feed Optional. Feed type.
 * @return string
 */
function get_tag_feed_link($tag_id, $feed = '') {
	$tag_id = (int) $tag_id;

	$tag = get_tag($tag_id);

	if ( empty($tag) || is_wp_error($tag) )
		return false;

	$permalink_structure = get_option('permalink_structure');

	if ( empty($feed) )
		$feed = get_default_feed();

	if ( '' == $permalink_structure ) {
		$link = get_option('home') . "?feed=$feed&amp;tag=" . $tag->slug;
	} else {
		$link = get_tag_link($tag->term_id);
		if ( $feed == get_default_feed() )
			$feed_link = 'feed';
		else
			$feed_link = "feed/$feed";
		$link = trailingslashit($link) . user_trailingslashit($feed_link, 'feed');
	}

	$link = apply_filters('tag_feed_link', $link, $feed);

	return $link;
}

/**
 * Retrieve edit tag link.
 *
 * @since 2.7.0
 *
 * @param int $tag_id Tag ID
 * @return string
 */
function get_edit_tag_link( $tag_id = 0 ) {
	$tag = get_term($tag_id, 'post_tag');

	if ( !current_user_can('manage_categories') )
		return;

	$location = admin_url('edit-tags.php?action=edit&amp;tag_ID=') . $tag->term_id;
	return apply_filters( 'get_edit_tag_link', $location );
}

/**
 * Display or retrieve edit tag link with formatting.
 *
 * @since 2.7.0
 *
 * @param string $link Optional. Anchor text.
 * @param string $before Optional. Display before edit link.
 * @param string $after Optional. Display after edit link.
 * @param int|object $tag Tag object or ID
 * @return string|null HTML content, if $echo is set to false.
 */
function edit_tag_link( $link = '', $before = '', $after = '', $tag = null ) {
	$tag = get_term($tag, 'post_tag');

	if ( !current_user_can('manage_categories') )
		return;

	if ( empty($link) )
		$link = __('Edit This');

	$link = '<a href="' . get_edit_tag_link( $tag->term_id ) . '" title="' . __( 'Edit tag' ) . '">' . $link . '</a>';
	echo $before . apply_filters( 'edit_tag_link', $link, $tag->term_id ) . $after;
}

/**
 * Retrieve the permalink for the feed of the search results.
 *
 * @since 2.5.0
 *
 * @param string $search_query Optional. Search query.
 * @param string $feed Optional. Feed type.
 * @return string
 */
function get_search_feed_link($search_query = '', $feed = '') {
	if ( empty($search_query) )
		$search = attribute_escape(get_search_query());
	else
		$search = attribute_escape(stripslashes($search_query));

	if ( empty($feed) )
		$feed = get_default_feed();

	$link = get_option('home') . "?s=$search&amp;feed=$feed";

	$link = apply_filters('search_feed_link', $link);

	return $link;
}

/**
 * Retrieve the permalink for the comments feed of the search results.
 *
 * @since 2.5.0
 *
 * @param string $search_query Optional. Search query.
 * @param string $feed Optional. Feed type.
 * @return string
 */
function get_search_comments_feed_link($search_query = '', $feed = '') {
	if ( empty($search_query) )
		$search = attribute_escape(get_search_query());
	else
		$search = attribute_escape(stripslashes($search_query));

	if ( empty($feed) )
		$feed = get_default_feed();

	$link = get_option('home') . "?s=$search&amp;feed=comments-$feed";

	$link = apply_filters('search_feed_link', $link);

	return $link;
}

/**
 * Retrieve edit posts link for post.
 *
 * Can be used within the WordPress loop or outside of it. Can be used with
 * pages, posts, attachments, and revisions.
 *
 * @since 2.3.0
 *
 * @param int $id Optional. Post ID.
 * @param string $context Optional, default to display. How to write the '&', defaults to '&amp;'.
 * @return string
 */
function get_edit_post_link( $id = 0, $context = 'display' ) {
	if ( !$post = &get_post( $id ) )
		return;

	if ( 'display' == $context )
		$action = 'action=edit&amp;';
	else
		$action = 'action=edit&';

	switch ( $post->post_type ) :
	case 'page' :
		if ( !current_user_can( 'edit_page', $post->ID ) )
			return;
		$file = 'page';
		$var  = 'post';
		break;
	case 'attachment' :
		if ( !current_user_can( 'edit_post', $post->ID ) )
			return;
		$file = 'media';
		$var  = 'attachment_id';
		break;
	case 'revision' :
		if ( !current_user_can( 'edit_post', $post->ID ) )
			return;
		$file = 'revision';
		$var  = 'revision';
		$action = '';
		break;
	default :
		if ( !current_user_can( 'edit_post', $post->ID ) )
			return;
		$file = 'post';
		$var  = 'post';
		break;
	endswitch;

	return apply_filters( 'get_edit_post_link', admin_url("$file.php?{$action}$var=$post->ID"), $post->ID, $context );
}

/**
 * Retrieve edit posts link for post.
 *
 * @since 1.0.0
 *
 * @param string $link Optional. Anchor text.
 * @param string $before Optional. Display before edit link.
 * @param string $after Optional. Display after edit link.
 */
function edit_post_link( $link = 'Edit This', $before = '', $after = '' ) {
	global $post;

	if ( $post->post_type == 'page' ) {
		if ( !current_user_can( 'edit_page', $post->ID ) )
			return;
	} else {
		if ( !current_user_can( 'edit_post', $post->ID ) )
			return;
	}

	$link = '<a href="' . get_edit_post_link( $post->ID ) . '" title="' . attribute_escape( __( 'Edit post' ) ) . '">' . $link . '</a>';
	echo $before . apply_filters( 'edit_post_link', $link, $post->ID ) . $after;
}

/**
 * Retrieve edit comment link.
 *
 * @since 2.3.0
 *
 * @param int $comment_id Optional. Comment ID.
 * @return string
 */
function get_edit_comment_link( $comment_id = 0 ) {
	$comment = &get_comment( $comment_id );
	$post = &get_post( $comment->comment_post_ID );

	if ( $post->post_type == 'page' ) {
		if ( !current_user_can( 'edit_page', $post->ID ) )
			return;
	} else {
		if ( !current_user_can( 'edit_post', $post->ID ) )
			return;
	}

	$location = admin_url('comment.php?action=editcomment&amp;c=') . $comment->comment_ID;
	return apply_filters( 'get_edit_comment_link', $location );
}

/**
 * Display or retrieve edit comment link with formatting.
 *
 * @since 1.0.0
 *
 * @param string $link Optional. Anchor text.
 * @param string $before Optional. Display before edit link.
 * @param string $after Optional. Display after edit link.
 * @return string|null HTML content, if $echo is set to false.
 */
function edit_comment_link( $link = 'Edit This', $before = '', $after = '' ) {
	global $comment, $post;

	if ( $post->post_type == 'attachment' ) {
	} elseif ( $post->post_type == 'page' ) {
		if ( !current_user_can( 'edit_page', $post->ID ) )
			return;
	} else {
		if ( !current_user_can( 'edit_post', $post->ID ) )
			return;
	}

	$link = '<a href="' . get_edit_comment_link( $comment->comment_ID ) . '" title="' . __( 'Edit comment' ) . '">' . $link . '</a>';
	echo $before . apply_filters( 'edit_comment_link', $link, $comment->comment_ID ) . $after;
}

/**
 * Display edit bookmark (literally a URL external to blog) link.
 *
 * @since 2.7.0
 *
 * @param int $link Optional. Bookmark ID.
 * @return string
 */
function get_edit_bookmark_link( $link = 0 ) {
	$link = get_bookmark( $link );

	if ( !current_user_can('manage_links') )
		return;

	$location = admin_url('link.php?action=edit&amp;link_id=') . $link->link_id;
	return apply_filters( 'get_edit_bookmark_link', $location, $link->link_id );
}

/**
 * Display edit bookmark (literally a URL external to blog) link anchor content.
 *
 * @since 2.7.0
 *
 * @param string $link Optional. Anchor text.
 * @param string $before Optional. Display before edit link.
 * @param string $after Optional. Display after edit link.
 * @param int $bookmark Optional. Bookmark ID.
 */
function edit_bookmark_link( $link = '', $before = '', $after = '', $bookmark = null ) {
	$bookmark = get_bookmark($bookmark);

	if ( !current_user_can('manage_links') )
		return;

	if ( empty($link) )
		$link = __('Edit This');

	$link = '<a href="' . get_edit_bookmark_link( $link ) . '" title="' . __( 'Edit link' ) . '">' . $link . '</a>';
	echo $before . apply_filters( 'edit_bookmark_link', $link, $bookmark->link_id ) . $after;
}

// Navigation links

/**
 * Retrieve previous post link that is adjacent to current post.
 *
 * @since 1.5.0
 *
 * @param bool $in_same_cat Optional. Whether link should be in same category.
 * @param string $excluded_categories Optional. Excluded categories IDs.
 * @return string
 */
function get_previous_post($in_same_cat = false, $excluded_categories = '') {
	return get_adjacent_post($in_same_cat, $excluded_categories);
}

/**
 * Retrieve next post link that is adjacent to current post.
 *
 * @since 1.5.0
 *
 * @param bool $in_same_cat Optional. Whether link should be in same category.
 * @param string $excluded_categories Optional. Excluded categories IDs.
 * @return string
 */
function get_next_post($in_same_cat = false, $excluded_categories = '') {
	return get_adjacent_post($in_same_cat, $excluded_categories, false);
}

/**
 * Retrieve adjacent post link.
 *
 * Can either be next or previous post link.
 *
 * @since 2.5.0
 *
 * @param bool $in_same_cat Optional. Whether link should be in same category.
 * @param string $excluded_categories Optional. Excluded categories IDs.
 * @param bool $previous Optional. Whether to retrieve previous post.
 * @return string
 */
function get_adjacent_post($in_same_cat = false, $excluded_categories = '', $previous = true) {
	global $post, $wpdb;

	if( empty($post) || !is_single() || is_attachment() )
		return null;

	$current_post_date = $post->post_date;

	$join = '';
	$posts_in_ex_cats_sql = '';
	if ( $in_same_cat || !empty($excluded_categories) ) {
		$join = " INNER JOIN $wpdb->term_relationships AS tr ON p.ID = tr.object_id INNER JOIN $wpdb->term_taxonomy tt ON tr.term_taxonomy_id = tt.term_taxonomy_id";

		if ( $in_same_cat ) {
			$cat_array = wp_get_object_terms($post->ID, 'category', 'fields=ids');
			$join .= " AND tt.taxonomy = 'category' AND tt.term_id IN (" . implode(',', $cat_array) . ")";
		}

		$posts_in_ex_cats_sql = "AND tt.taxonomy = 'category'";
		if ( !empty($excluded_categories) ) {
			$excluded_categories = array_map('intval', explode(' and ', $excluded_categories));
			if ( !empty($cat_array) ) {
				$excluded_categories = array_diff($excluded_categories, $cat_array);
				$posts_in_ex_cats_sql = '';
			}

			if ( !empty($excluded_categories) ) {
				$posts_in_ex_cats_sql = " AND tt.taxonomy = 'category' AND tt.term_id NOT IN (" . implode($excluded_categories, ',') . ')';
			}
		}
	}

	$adjacent = $previous ? 'previous' : 'next';
	$op = $previous ? '<' : '>';
	$order = $previous ? 'DESC' : 'ASC';

	$join  = apply_filters( "get_{$adjacent}_post_join", $join, $in_same_cat, $excluded_categories );
	$where = apply_filters( "get_{$adjacent}_post_where", $wpdb->prepare("WHERE p.post_date $op %s AND p.post_type = 'post' AND p.post_status = 'publish' $posts_in_ex_cats_sql", $current_post_date), $in_same_cat, $excluded_categories );
	$sort  = apply_filters( "get_{$adjacent}_post_sort", "ORDER BY p.post_date $order LIMIT 1" );

	return $wpdb->get_row("SELECT p.* FROM $wpdb->posts AS p $join $where $sort");
}

/**
 * Display previous post link that is adjacent to the current post.
 *
 * @since 1.5.0
 *
 * @param string $format Optional. Link anchor format.
 * @param string $link Optional. Link permalink format.
 * @param bool $in_same_cat Optional. Whether link should be in same category.
 * @param string $excluded_categories Optional. Excluded categories IDs.
 */
function previous_post_link($format='&laquo; %link', $link='%title', $in_same_cat = false, $excluded_categories = '') {
	adjacent_post_link($format, $link, $in_same_cat, $excluded_categories, true);
}

/**
 * Display next post link that is adjacent to the current post.
 *
 * @since 1.5.0
 *
 * @param string $format Optional. Link anchor format.
 * @param string $link Optional. Link permalink format.
 * @param bool $in_same_cat Optional. Whether link should be in same category.
 * @param string $excluded_categories Optional. Excluded categories IDs.
 */
function next_post_link($format='%link &raquo;', $link='%title', $in_same_cat = false, $excluded_categories = '') {
	adjacent_post_link($format, $link, $in_same_cat, $excluded_categories, false);
}

/**
 * Display adjacent post link.
 *
 * Can be either next post link or previous.
 *
 * @since 2.5.0
 *
 * @param string $format Link anchor format.
 * @param string $link Link permalink format.
 * @param bool $in_same_cat Optional. Whether link should be in same category.
 * @param string $excluded_categories Optional. Excluded categories IDs.
 * @param bool $previous Optional, default is true. Whether display link to previous post.
 */
function adjacent_post_link($format, $link, $in_same_cat = false, $excluded_categories = '', $previous = true) {
	if ( $previous && is_attachment() )
		$post = & get_post($GLOBALS['post']->post_parent);
	else
		$post = get_adjacent_post($in_same_cat, $excluded_categories, $previous);

	if ( !$post )
		return;

	$title = $post->post_title;

	if ( empty($post->post_title) )
		$title = $previous ? __('Previous Post') : __('Next Post');

	$title = apply_filters('the_title', $title, $post);
	$date = mysql2date(get_option('date_format'), $post->post_date);

	$string = '<a href="'.get_permalink($post).'">';
	$link = str_replace('%title', $title, $link);
	$link = str_replace('%date', $date, $link);
	$link = $string . $link . '</a>';

	$format = str_replace('%link', $link, $format);

	$adjacent = $previous ? 'previous' : 'next';
	echo apply_filters( "{$adjacent}_post_link", $format, $link );
}

/**
 * Retrieve get links for page numbers.
 *
 * @since 1.5.0
 *
 * @param int $pagenum Optional. Page ID.
 * @return string
 */
function get_pagenum_link($pagenum = 1) {
	global $wp_rewrite;

	$pagenum = (int) $pagenum;

	$request = remove_query_arg( 'paged' );

	$home_root = parse_url(get_option('home'));
	$home_root = ( isset($home_root['path']) ) ? $home_root['path'] : '';
	$home_root = preg_quote( trailingslashit( $home_root ), '|' );

	$request = preg_replace('|^'. $home_root . '|', '', $request);
	$request = preg_replace('|^/+|', '', $request);

	if ( !$wp_rewrite->using_permalinks() || is_admin() ) {
		$base = trailingslashit( get_bloginfo( 'home' ) );

		if ( $pagenum > 1 ) {
			$result = add_query_arg( 'paged', $pagenum, $base . $request );
		} else {
			$result = $base . $request;
		}
	} else {
		$qs_regex = '|\?.*?$|';
		preg_match( $qs_regex, $request, $qs_match );

		if ( !empty( $qs_match[0] ) ) {
			$query_string = $qs_match[0];
			$request = preg_replace( $qs_regex, '', $request );
		} else {
			$query_string = '';
		}

		$request = preg_replace( '|page/\d+/?$|', '', $request);
		$request = preg_replace( '|^index\.php|', '', $request);
		$request = ltrim($request, '/');

		$base = trailingslashit( get_bloginfo( 'url' ) );

		if ( $wp_rewrite->using_index_permalinks() && ( $pagenum > 1 || '' != $request ) )
			$base .= 'index.php/';

		if ( $pagenum > 1 ) {
			$request = ( ( !empty( $request ) ) ? trailingslashit( $request ) : $request ) . user_trailingslashit( 'page/' . $pagenum, 'paged' );
		}

		$result = $base . $request . $query_string;
	}

	$result = apply_filters('get_pagenum_link', $result);

	return $result;
}

/**
 * Retrieve next posts pages link.
 *
 * Backported from 2.1.3 to 2.0.10.
 *
 * @since 2.0.10
 *
 * @param int $max_page Optional. Max pages.
 * @return string
 */
function get_next_posts_page_link($max_page = 0) {
	global $paged;

	if ( !is_single() ) {
		if ( !$paged )
			$paged = 1;
		$nextpage = intval($paged) + 1;
		if ( !$max_page || $max_page >= $nextpage )
			return get_pagenum_link($nextpage);
	}
}

/**
 * Display or return the next posts pages link.
 *
 * @since 0.71
 *
 * @param int $max_page Optional. Max pages.
 * @param boolean $echo Optional. Echo or return;
 */
function next_posts( $max_page = 0, $echo = true ) {
	$output = clean_url( get_next_posts_page_link( $max_page ) );

	if ( $echo )
		echo $output;
	else
		return $output;
}

/**
 * Return the next posts pages link.
 *
 * @since 2.7.0
 *
 * @param string $label Content for link text.
 * @param int $max_page Optional. Max pages.
 * @return string|null
 */
function get_next_posts_link( $label = 'Next Page &raquo;', $max_page = 0 ) {
	global $paged, $wp_query;

	if ( !$max_page ) {
		$max_page = $wp_query->max_num_pages;
	}

	if ( !$paged )
		$paged = 1;

	$nextpage = intval($paged) + 1;

	if ( !is_single() && ( empty($paged) || $nextpage <= $max_page) ) {
		$attr = apply_filters( 'next_posts_link_attributes', '' );
		return '<a href="' . next_posts( $max_page, false ) . "\" $attr>". preg_replace('/&([^#])(?![a-z]{1,8};)/', '&#038;$1', $label) .'</a>';
	}
}

/**
 * Display the next posts pages link.
 *
 * @since 0.71
 * @uses get_next_posts_link()
 *
 * @param string $label Content for link text.
 * @param int $max_page Optional. Max pages.
 */
function next_posts_link( $label = 'Next Page &raquo;', $max_page = 0 ) {
	echo get_next_posts_link( $label, $max_page );
}

/**
 * Retrieve previous post pages link.
 *
 * Will only return string, if not on a single page or post.
 *
 * Backported to 2.0.10 from 2.1.3.
 *
 * @since 2.0.10
 *
 * @return string|null
 */
function get_previous_posts_page_link() {
	global $paged;

	if ( !is_single() ) {
		$nextpage = intval($paged) - 1;
		if ( $nextpage < 1 )
			$nextpage = 1;
		return get_pagenum_link($nextpage);
	}
}

/**
 * Display or return the previous posts pages link.
 *
 * @since 0.71
 *
 * @param boolean $echo Optional. Echo or return;
 */
function previous_posts( $echo = true ) {
	$output = clean_url( get_previous_posts_page_link() );

	if ( $echo )
		echo $output;
	else
		return $output;
}

/**
 * Return the previous posts pages link.
 *
 * @since 2.7.0
 *
 * @param string $label Optional. Previous page link text.
 * @return string|null
 */
function get_previous_posts_link( $label = '&laquo; Previous Page' ) {
	global $paged;

	if ( !is_single() && $paged > 1 ) {
		$attr = apply_filters( 'previous_posts_link_attributes', '' );
		return '<a href="' . previous_posts( false ) . "\" $attr>". preg_replace( '/&([^#])(?![a-z]{1,8};)/', '&#038;$1', $label ) .'</a>';
	}
}

/**
 * Display the previous posts page link.
 *
 * @since 0.71
 * @uses get_previous_posts_link()
 *
 * @param string $label Optional. Previous page link text.
 */
function previous_posts_link( $label = '&laquo; Previous Page' ) {
	echo get_previous_posts_link( $label );
}

/**
 * Display post pages link navigation for previous and next pages.
 *
 * @since 0.71
 *
 * @param string $sep Optional. Separator for posts navigation links.
 * @param string $prelabel Optional. Label for previous pages.
 * @param string $nxtlabel Optional Label for next pages.
 */
function posts_nav_link( $sep = ' &#8212; ', $prelabel = '&laquo; Previous Page', $nxtlabel = 'Next Page &raquo;' ) {
	global $wp_query;
	if ( !is_singular() ) {
		$max_num_pages = $wp_query->max_num_pages;
		$paged = get_query_var('paged');

		//only have sep if there's both prev and next results
		if ($paged < 2 || $paged >= $max_num_pages) {
			$sep = '';
		}

		if ( $max_num_pages > 1 ) {
			previous_posts_link($prelabel);
			echo preg_replace('/&([^#])(?![a-z]{1,8};)/', '&#038;$1', $sep);
			next_posts_link($nxtlabel);
		}
	}
}

/**
 * Retrieve page numbers links.
 *
 * @since 2.7.0
 *
 * @param int $pagenum Optional. Page number.
 * @return string
 */
function get_comments_pagenum_link( $pagenum = 1, $max_page = 0 ) {
	global $post, $wp_rewrite;

	$pagenum = (int) $pagenum;

	$result = get_permalink( $post->ID );

	if ( 'newest' == get_option('default_comments_page') ) {
		if ( $pagenum != $max_page ) {
			if ( $wp_rewrite->using_permalinks() )
				$result = user_trailingslashit( trailingslashit($result) . 'comment-page-' . $pagenum, 'commentpaged');
			else
				$result = add_query_arg( 'cpage', $pagenum, $result );
		}
	} elseif ( $pagenum > 1 ) {
		if ( $wp_rewrite->using_permalinks() )
			$result = user_trailingslashit( trailingslashit($result) . 'comment-page-' . $pagenum, 'commentpaged');
		else
			$result = add_query_arg( 'cpage', $pagenum, $result );
	}

	$result .= '#comments';

	$result = apply_filters('get_comments_pagenum_link', $result);

	return $result;
}

/**
 * Display link to next comments pages.
 *
 * @since 2.7.0
 *
 * @param string $label Optional. Label for link text.
 * @param int $max_page Optional. Max page.
 */
function next_comments_link($label='', $max_page = 0) {
	global $wp_query;

	if ( !is_singular() )
		return;

	$page = get_query_var('cpage');

	if ( !$page )
		$page = 1;

	$nextpage = intval($page) + 1;

	if ( empty($max_page) )
		$max_page = $wp_query->max_num_comment_pages;

	if ( empty($max_page) )
		$max_page = get_comment_pages_count();

	if ( $nextpage > $max_page )
		return;

	if ( empty($label) )
		$label = __('Newer Comments &raquo;');

	echo '<a href="' . clean_url( get_comments_pagenum_link( $nextpage, $max_page ) );
	$attr = apply_filters( 'next_comments_link_attributes', '' );
	echo "\" $attr>". preg_replace('/&([^#])(?![a-z]{1,8};)/', '&#038;$1', $label) .'</a>';
}

/**
 * Display the previous comments page link.
 *
 * @since 2.7.0
 *
 * @param string $label Optional. Label for comments link text.
 */
function previous_comments_link($label='') {

	if ( !is_singular() )
		return;

	$page = get_query_var('cpage');

	if ( !$page )
		$page = 1;

	if ( $page <= 1 )
		return;

	$prevpage = intval($page) - 1;

	if ( empty($label) )
		$label = __('&laquo; Older Comments');

	echo '<a href="' . clean_url(get_comments_pagenum_link($prevpage));
	$attr = apply_filters( 'previous_comments_link_attributes', '' );
	echo "\" $attr>". preg_replace('/&([^#])(?![a-z]{1,8};)/', '&#038;$1', $label) .'</a>';
}

/**
 * Create pagination links for the comments on the current post.
 *
 * @see paginate_links()
 * @since 2.7.0
 *
 * @param string|array $args Optional args. See paginate_links.
 * @return string Markup for pagination links.
*/
function paginate_comments_links($args = array()) {
	global $wp_query, $wp_rewrite;

	if ( !is_singular() )
		return;

	$page = get_query_var('cpage');
	if ( !$page )
		$page = 1;
	$max_page = get_comment_pages_count();
	$defaults = array(
		'base' => add_query_arg( 'cpage', '%#%' ),
		'format' => '',
		'total' => $max_page,
		'current' => $page,
		'echo' => true,
		'add_fragment' => '#comments'
	);
	if ( $wp_rewrite->using_permalinks() )
		$defaults['base'] = user_trailingslashit(get_permalink() . 'comment-page-%#%', 'commentpaged');

	$args = wp_parse_args( $args, $defaults );
	$page_links = paginate_links( $args );

	if ( $args['echo'] )
		echo $page_links;
	else
		return $page_links;
}

/**
 * Retrieve shortcut link.
 *
 * Use this in 'a' element 'href' attribute.
 *
 * @since 2.6.0
 *
 * @return string
 */
function get_shortcut_link() {
	$link = "javascript:
			var d=document,
			w=window,
			e=w.getSelection,
			k=d.getSelection,
			x=d.selection,
			s=(e?e():(k)?k():(x?x.createRange().text:0)),
			f='" . admin_url('press-this.php') . "',
			l=d.location,
			e=encodeURIComponent,
			g=f+'?u='+e(l.href)+'&t='+e(d.title)+'&s='+e(s)+'&v=2';
			function a(){
				if(!w.open(g,'t','toolbar=0,resizable=0,scrollbars=1,status=1,width=720,height=570')){
					l.href=g;
				}
			}";
			if (strpos($_SERVER['HTTP_USER_AGENT'], 'Firefox') !== false)
				$link .= 'setTimeout(a,0);';
			else
				$link .= 'a();';

			$link .= "void(0);";

	$link = str_replace(array("\r", "\n", "\t"),  '', $link);

	return apply_filters('shortcut_link', $link);
}

/**
 * Retrieve the site url.
 *
 * Returns the 'site_url' option with the appropriate protocol,  'https' if
 * is_ssl() and 'http' otherwise. If $scheme is 'http' or 'https', is_ssl() is
 * overridden.
 *
 * @package WordPress
 * @since 2.6.0
 *
 * @param string $path Optional. Path relative to the site url.
 * @param string $scheme Optional. Scheme to give the site url context. Currently 'http','https', 'login', 'login_post', or 'admin'.
 * @return string Site url link with optional path appended.
*/
function site_url($path = '', $scheme = null) {
	// should the list of allowed schemes be maintained elsewhere?
	$orig_scheme = $scheme;
	if ( !in_array($scheme, array('http', 'https')) ) {
		if ( ('login_post' == $scheme) && ( force_ssl_login() || force_ssl_admin() ) )
			$scheme = 'https';
		elseif ( ('login' == $scheme) && ( force_ssl_admin() ) )
			$scheme = 'https';
		elseif ( ('admin' == $scheme) && force_ssl_admin() )
			$scheme = 'https';
		else
			$scheme = ( is_ssl() ? 'https' : 'http' );
	}

	$url = str_replace( 'http://', "{$scheme}://", get_option('siteurl') );

	if ( !empty($path) && is_string($path) && strpos($path, '..') === false )
		$url .= '/' . ltrim($path, '/');

	return apply_filters('site_url', $url, $path, $orig_scheme);
}

/**
 * Retrieve the url to the admin area.
 *
 * @package WordPress
 * @since 2.6.0
 *
 * @param string $path Optional path relative to the admin url
 * @return string Admin url link with optional path appended
*/
function admin_url($path = '') {
	$url = site_url('wp-admin/', 'admin');

	if ( !empty($path) && is_string($path) && strpos($path, '..') === false )
		$url .= ltrim($path, '/');

	return $url;
}

/**
 * Retrieve the url to the includes directory.
 *
 * @package WordPress
 * @since 2.6.0
 *
 * @param string $path Optional. Path relative to the includes url.
 * @return string Includes url link with optional path appended.
*/
function includes_url($path = '') {
	$url = site_url() . '/' . WPINC . '/';

	if ( !empty($path) && is_string($path) && strpos($path, '..') === false )
		$url .= ltrim($path, '/');

	return $url;
}

/**
 * Retrieve the url to the content directory.
 *
 * @package WordPress
 * @since 2.6.0
 *
 * @param string $path Optional. Path relative to the content url.
 * @return string Content url link with optional path appended.
*/
function content_url($path = '') {
	$scheme = ( is_ssl() ? 'https' : 'http' );
	$url = WP_CONTENT_URL;
	if ( 0 === strpos($url, 'http') ) {
		if ( is_ssl() )
			$url = str_replace( 'http://', "{$scheme}://", $url );
	}

	if ( !empty($path) && is_string($path) && strpos($path, '..') === false )
		$url .= '/' . ltrim($path, '/');

	return $url;
}

/**
 * Retrieve the url to the plugins directory.
 *
 * @package WordPress
 * @since 2.6.0
 *
 * @param string $path Optional. Path relative to the plugins url.
 * @return string Plugins url link with optional path appended.
*/
function plugins_url($path = '') {
	$scheme = ( is_ssl() ? 'https' : 'http' );
	$url = WP_PLUGIN_URL;
	if ( 0 === strpos($url, 'http') ) {
		if ( is_ssl() )
			$url = str_replace( 'http://', "{$scheme}://", $url );
	}

	if ( !empty($path) && is_string($path) && strpos($path, '..') === false )
		$url .= '/' . ltrim($path, '/');

	return $url;
}

?>
