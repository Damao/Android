<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the id=main div and all content after
 *
 * @package WordPress
 * @subpackage Twenty_Eleven
 * @since Android 1.0
 */
?>

</div><!-- #main -->

<footer id="footer" class="wrap" role="contentinfo">
    <div class="lay_wrap">
        <?php do_action('android_credits'); ?>
        <?php bloginfo('description'); ?>.
        <a href="<?php echo esc_url(__('http://wordpress.org/', 'android')); ?>"
           title="<?php esc_attr_e('Semantic Personal Publishing Platform', 'android'); ?>" rel="generator"
           target="_blank"><?php printf(__('%s', 'android'), 'WordPress'); ?></a>
        &amp;&amp; <a href="http://ooxx.me/theme-android.orz" title="Android Developer Style Theme" target="_blank">Android</a>
    </div>
</footer><!-- #colophon -->
</div><!-- #page -->

<?php wp_footer(); ?>
<script src="<?php bloginfo('stylesheet_directory'); ?>/js/jquery.js"></script>
<script type="text/javascript">
    var home_url="<?php echo esc_url(home_url('/')); ?>";
</script>
<script src="<?php bloginfo('stylesheet_directory'); ?>/js/android.js"></script>

</body>
</html>