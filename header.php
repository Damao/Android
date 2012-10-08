<?php
/**
 * The Header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="main">
 *
 * @package WordPress
 * @subpackage Twenty_Eleven
 * @since Android 1.0
 */
?><!DOCTYPE html>
<!--[if IE 6]>
<html id="ie6" class="is_ie" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 7]>
<html id="ie7" class="is_ie" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 8]>
<html id="ie8" class="is_ie" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 9]>
<html id="ie9" class="is_ie" <?php language_attributes(); ?>>
<![endif]-->
<!--[if !(IE 6) | !(IE 7) | !(IE 8)  ]><!-->
<html <?php if(is_mobile()){echo 'class="is_mobile" ';} language_attributes(); ?>>
<!--<![endif]-->
<head>
    <meta charset="<?php bloginfo('charset'); ?>"/>
    <meta name="viewport" content="width=device-width"/>
    <title><?php
        /*
       * Print the <title> tag based on what is being viewed.
       */
        global $page, $paged;

        wp_title('|', true, 'right');

        // Add the blog name.
        bloginfo('name');

        // Add the blog description for the home/front page.
        $site_description = get_bloginfo('description', 'display');
        if ($site_description && (is_home() || is_front_page()))
            echo " | $site_description";

        // Add a page number if necessary:
        if ($paged >= 2 || $page >= 2)
            echo ' | ' . sprintf(__('Page %s', 'android'), max($paged, $page));

        ?></title>
    <link rel="profile" href="http://gmpg.org/xfn/11"/>
    <link rel="stylesheet" type="text/css" media="all" href="<?php bloginfo('stylesheet_url'); ?>"/>
    <link rel="pingback" href="<?php bloginfo('pingback_url'); ?>"/>

    <script type="text/javascript">

        var _gaq = _gaq || [];
        _gaq.push(['_setAccount', 'UA-6006493-1']);
        _gaq.push(['_trackPageview']);

        (function() {
            var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
            ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
            var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
        })();

    </script>

    <!--[if lt IE 9]>
    <script src="<?php echo get_template_directory_uri(); ?>/js/html5.js" type="text/javascript"></script>
    <![endif]-->
    <?php
    /* We add some JavaScript to pages with the comment form
      * to support sites with threaded comments (when in use).
      */
    if (is_singular() && get_option('thread_comments'))
//        wp_enqueue_script('comment-reply'); 减少请求数

    /* Always have wp_head() just before the closing </head>
      * tag of your theme, or you will break many plugins, which
      * generally use this hook to add elements to <head> such
      * as styles, scripts, and meta tags.
      */
    wp_head();
    ?>
</head>

<body <?php body_class(); ?>>
<!-- Header -->
<header id="header">
    <div class="lay_wrap clearfix" id="header-wrap">
        <div class="col-3 logo">
            <a href="<?php echo esc_url(home_url('/')); ?>"
               title="<?php echo esc_attr(get_bloginfo('name', 'display')); ?>"
               rel="home"><span class="link-logo"></span><span class="blog-name"><?php bloginfo('name'); ?></span></a>
            <div class="btn-quicknav" id="btn-quicknav">
                <a href="#" class="arrow-inactive">Quicknav</a>
                <a href="#" class="arrow-active">Quicknav</a>
            </div>
        </div>
        <?php
//list terms in a given taxonomy using wp_list_categories (also useful as a widget if using a PHP Code plugin)
        $categories = get_the_category(); //get all categories for this post
        $args = array(
            'orderby' => 'id',
            'show_count' => 0,
            'pad_counts' => 0,
            'hierarchical' => 1,
            'current_category' => $categories[0]->cat_ID,
            'depth' => 1,
            'title_li' => ''
        );
        ?>
        <ul class="nav-x col-9">
            <?php wp_list_categories($args); ?>
        </ul>

        <!-- New Search -->
        <div class="menu-container">
            <div class="moremenu">
                <div id="more-btn"></div>
            </div>
            <div class="morehover" id="moremenu">
                <div class="top"></div>
                <div class="mid">
                    <div class="header">Pages</div>
                    <ul>
                        <?php wp_list_pages('title_li='); ?>
                    </ul>
                    <div class="header">RSS</div>
                    <ul>
                        <li><a href="<?php bloginfo('rss2_url'); ?>" target="_blank">订阅<?php bloginfo('name'); ?></a></li>
                    </ul>
                </div>
                <div class="bottom"></div>
            </div>
            <?php get_search_form(); ?>


        </div>
        <!-- /New Search>


     <!-- Expanded quicknav -->
        <div id="quicknav" class="col-9">
            <ul>
                <?php
                $args = array(
                    'orderby' => 'id'
                );
                $categories = get_categories($args);
                foreach ($categories as $category) {
                    echo '<li class="' . $category->name . '"><ul>';
                    query_posts('showposts=5&cat=' . $category->term_id);
                    while (have_posts()) : the_post();
                        ?>
                        <li><a href="<?php the_permalink() ?>"
                               title="<?php the_title(); ?>"><?php the_title(); ?></a></li>
                        <?php endwhile;
                    wp_reset_query();
                    echo "</ul></li>";
                } ?>
            </ul>
        </div>
        <!-- /Expanded quicknav -->
    </div>
</header>
<!-- /Header -->


<div class="clearfix" id="body-content">
     <div class="lay_wrap">