<?php
/**
 * The Sidebar containing the main widget area.
 *
 * @package WordPress
 * @subpackage Twenty_Eleven
 * @since Android 1.0
 */
?>
<?php if (!is_mobile()) { ?>
<div id="side-nav" itemscope="" itemtype="http://schema.org/SiteNavigationElement">
    <div id="devdoc-nav">
        <a class="totop" href="#top" data-g-event="left-nav-top">To Top</a>
        <ul id="smart-nav">

            <?php if (is_singular()) { ?>
            <li id="smart-nav-related" class="nav-section">
                <div class="nav-section-header"><a href="javascript:vold(0)">Related</a></div>
                <ul>
                    <?php
                    android_related();
                    ?>
                </ul>
            </li>
            <?php }?>
            <li id="smart-nav-recent" class="nav-section">
                <div class="nav-section-header"><a href="javascript:vold(0)">Recent</a></div>
                <ul>
                    <?php
                    if (is_category()) {
                        query_posts('showposts=5&cat=' . get_query_var('cat'));
                    } else if (is_tag()) {
                        query_posts('showposts=5&tag=' . get_query_var('tag'));
                    } else {
                        query_posts('showposts=5');
                    }
                    while (have_posts()) : the_post();
                        ?>
                        <li><a href="<?php the_permalink() ?>"
                               title="<?php the_title(); ?>"><?php the_title(); ?></a></li>
                        <?php endwhile;
                    wp_reset_query();
                    ?>
                </ul>
            </li>
        </ul>


    </div>

</div>
<?php } ?>