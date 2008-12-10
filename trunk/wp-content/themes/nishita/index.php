<?php get_header(); ?>

<?php if (is_home()) { query_posts('showposts=1'); } ?>
<?php if (have_posts()) : ?>
<?php while (have_posts()) : the_post(); ?>

<!-- BEGIN #photo -->
<h2 class="photo-title"><span><?php the_title(); ?></span></h2>
<div id="photo">
    <div id="photo-inner">
    <?php the_content(); ?>
    <?php the_tags('<hr />TAGS: ', ', ', ''); ?>
    </div>
</div>
<!-- END #photo -->

<!-- BEGIN photo meta -->
<div id="photo-meta">
    <div id="photo-meta-inner">
        <ul>
        <li><?php the_time('F jS, Y') ?></li>
        <li><?php the_category(', ') ?></li>
        <?php $wp_query->is_single = false; ?>
        <li><?php comments_popup_link('No comments', '1 comment', '% comments','Comments closed'); ?></li>
        <li><a href="<?php the_permalink() ?>" title="<?php the_title(); ?>">#</a></li>
        <?php edit_post_link(__('Edit'), '<li>', '</li>'); ?>
        </ul>
    </div>
</div>
<!-- END photo meta -->

<!-- BEGIN navigate -->
<div id="navigate">
    <div id="navigate-inner" class="clearfix">
        <?php $wp_query->is_single = true; ?>
        <span class="previous"><?php previous_post( '%', 'Previous', '' ) ?></span>
        <span class="next"><?php next_post( '%', 'Next', '' ) ?></span>
    </div>
</div>
<!-- END navigate -->
<?php endwhile; ?>
<?php endif; ?>

<?php get_footer(); ?>