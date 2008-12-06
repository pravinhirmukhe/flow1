<?php
/**
 * @package WordPress
 * @subpackage Classic_Theme
 */
?>
<!-- begin footer -->
</div>

<?php get_sidebar(); ?>

<p class="credit"><!--<?php echo get_num_queries(); ?> queries. <?php timer_stop(1); ?> seconds. --> <cite><?php echo sprintf(__("Powered by <a href='http://wordpress.org/' title='%s'><strong>WordPress</strong></a>"), __("Powered by WordPress, state-of-the-art semantic personal publishing platform.")); ?><?php echo sprintf(__(" <a href='http://wordpress.org/' title='%s'><strong>中文站</strong></a>"), __("提供 WordPress 主题, WordPress 插件, WordPress 程序等 WordPress 资源的中文资讯。")); ?></cite></p>

</div>

<?php wp_footer(); ?>
</body>
</html>