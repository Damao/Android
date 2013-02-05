<?php
/**
 * The template for displaying the footer.
 *
 * Contains footer content and the closing of the
 * #main and #page div elements.
 *
 * @package WordPress
 * @subpackage Twenty_Twelve
 * @since Twenty Twelve 1.0
 */
?>
	</div><!-- #main .wrapper -->
	<footer id="colophon" role="contentinfo">
		<div class="site-info">
			<?php do_action( 'twentytwelve_credits' ); ?>
            <span class="site-description"><?php bloginfo( 'description' ); ?></span> .
            <a href="<?php echo esc_url( __( 'http://wordpress.org/', 'twentytwelve' ) ); ?>" title="<?php esc_attr_e( 'Semantic Personal Publishing Platform', 'twentytwelve' ); ?>"><?php printf( __( '%s', 'twentytwelve' ), 'WordPress' ); ?></a> &&
            <a href="http://ooxx.me/theme-android.orz" title="Android Developer Style Theme" target="_blank">Android</a>
		</div><!-- .site-info -->
	</footer><!-- #colophon -->
</div><!-- #page -->

<?php wp_footer(); ?>
<script src="<?php bloginfo('stylesheet_directory'); ?>/js/jquery.js"></script>
<script type="text/javascript">
    var home_url="<?php echo esc_url(home_url('/')); ?>";
    var is_mobile="<?php if(is_mobile()){echo 'true';}?>";
</script>
<script src="<?php bloginfo('stylesheet_directory'); ?>/js/android.js?201302051332"></script>
</body>
</html>