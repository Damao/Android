<?php
/**
 * Twenty Twelve functions and definitions.
 *
 * Sets up the theme and provides some helper functions, which are used
 * in the theme as custom template tags. Others are attached to action and
 * filter hooks in WordPress to change core functionality.
 *
 * When using a child theme (see http://codex.wordpress.org/Theme_Development and
 * http://codex.wordpress.org/Child_Themes), you can override certain functions
 * (those wrapped in a function_exists() call) by defining them first in your child theme's
 * functions.php file. The child theme's functions.php file is included before the parent
 * theme's file, so the child theme functions would be used.
 *
 * Functions that are not pluggable (not wrapped in function_exists()) are instead attached
 * to a filter or action hook.
 *
 * For more information on hooks, actions, and filters, see http://codex.wordpress.org/Plugin_API.
 *
 * @package WordPress
 * @subpackage Twenty_Twelve
 * @since Twenty Twelve 1.0
 */

/**
 * Sets up the content width value based on the theme's design and stylesheet.
 */
if ( ! isset( $content_width ) )
	$content_width = 625;

/**
 * Sets up theme defaults and registers the various WordPress features that
 * Twenty Twelve supports.
 *
 * @uses load_theme_textdomain() For translation/localization support.
 * @uses add_editor_style() To add a Visual Editor stylesheet.
 * @uses add_theme_support() To add support for post thumbnails, automatic feed links,
 * 	custom background, and post formats.
 * @uses register_nav_menu() To add support for navigation menus.
 * @uses set_post_thumbnail_size() To set a custom post thumbnail size.
 *
 * @since Twenty Twelve 1.0
 */
function twentytwelve_setup() {
	/*
	 * Makes Twenty Twelve available for translation.
	 *
	 * Translations can be added to the /languages/ directory.
	 * If you're building a theme based on Twenty Twelve, use a find and replace
	 * to change 'twentytwelve' to the name of your theme in all the template files.
	 */
	load_theme_textdomain( 'twentytwelve', get_template_directory() . '/languages' );

	// This theme styles the visual editor with editor-style.css to match the theme style.
	add_editor_style();

	// Adds RSS feed links to <head> for posts and comments.
	add_theme_support( 'automatic-feed-links' );

	// This theme supports a variety of post formats.
	add_theme_support( 'post-formats', array( 'aside', 'image', 'link', 'quote', 'status' ) );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menu( 'primary', __( 'Primary Menu', 'twentytwelve' ) );

	/*
	 * This theme supports custom background color and image, and here
	 * we also set up the default background color.
	 */
	add_theme_support( 'custom-background', array(
		'default-color' => 'F9F9F9',
	) );

	// This theme uses a custom image size for featured images, displayed on "standard" posts.
	add_theme_support( 'post-thumbnails' );
	set_post_thumbnail_size( 624, 9999 ); // Unlimited height, soft crop
}
add_action( 'after_setup_theme', 'twentytwelve_setup' );

/**
 * Adds support for a custom header image.
 */
require( get_template_directory() . '/inc/custom-header.php' );

/**
 * Enqueues scripts and styles for front-end.
 *
 * @since Twenty Twelve 1.0
 */
function twentytwelve_scripts_styles() {
	global $wp_styles;

	/*
	 * Adds JavaScript to pages with the comment form to support
	 * sites with threaded comments (when in use).
	 */
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) )
		wp_enqueue_script( 'comment-reply' );

	/*
	 * Adds JavaScript for handling the navigation menu hide-and-show behavior.
	 */
	wp_enqueue_script( 'twentytwelve-navigation', get_template_directory_uri() . '/js/navigation.js', array(), '1.0', true );

	/*
	 * Loads our special font CSS file.
	 *
	 * The use of Open Sans by default is localized. For languages that use
	 * characters not supported by the font, the font can be disabled.
	 *
	 * To disable in a child theme, use wp_dequeue_style()
	 * function mytheme_dequeue_fonts() {
	 *     wp_dequeue_style( 'twentytwelve-fonts' );
	 * }
	 * add_action( 'wp_enqueue_scripts', 'mytheme_dequeue_fonts', 11 );
	 */

	/* translators: If there are characters in your language that are not supported
	   by Open Sans, translate this to 'off'. Do not translate into your own language. */
	if ( 'off' !== _x( 'on', 'Open Sans font: on or off', 'twentytwelve' ) ) {
		$subsets = 'latin,latin-ext';

		/* translators: To add an additional Open Sans character subset specific to your language, translate
		   this to 'greek', 'cyrillic' or 'vietnamese'. Do not translate into your own language. */
		$subset = _x( 'no-subset', 'Open Sans font: add new subset (greek, cyrillic, vietnamese)', 'twentytwelve' );

		if ( 'cyrillic' == $subset )
			$subsets .= ',cyrillic,cyrillic-ext';
		elseif ( 'greek' == $subset )
			$subsets .= ',greek,greek-ext';
		elseif ( 'vietnamese' == $subset )
			$subsets .= ',vietnamese';

		$protocol = is_ssl() ? 'https' : 'http';
		$query_args = array(
			'family' => 'Open+Sans:400italic,700italic,400,700',
			'subset' => $subsets,
		);
		wp_enqueue_style( 'twentytwelve-fonts', add_query_arg( $query_args, "$protocol://fonts.googleapis.com/css" ), array(), null );
	}

	/*
	 * Loads our main stylesheet.
	 */
	wp_enqueue_style( 'twentytwelve-style', get_stylesheet_uri() );

	/*
	 * Loads the Internet Explorer specific stylesheet.
	 */
	wp_enqueue_style( 'twentytwelve-ie', get_template_directory_uri() . '/css/ie.css', array( 'twentytwelve-style' ), '20121010' );
	$wp_styles->add_data( 'twentytwelve-ie', 'conditional', 'lt IE 9' );
}
add_action( 'wp_enqueue_scripts', 'twentytwelve_scripts_styles' );

/**
 * Creates a nicely formatted and more specific title element text
 * for output in head of document, based on current view.
 *
 * @since Twenty Twelve 1.0
 *
 * @param string $title Default title text for current view.
 * @param string $sep Optional separator.
 * @return string Filtered title.
 */
function twentytwelve_wp_title( $title, $sep ) {
	global $paged, $page;

	if ( is_feed() )
		return $title;

	// Add the site name.
	$title .= get_bloginfo( 'name' );

	// Add the site description for the home/front page.
	$site_description = get_bloginfo( 'description', 'display' );
	if ( $site_description && ( is_home() || is_front_page() ) )
		$title = "$title $sep $site_description";

	// Add a page number if necessary.
	if ( $paged >= 2 || $page >= 2 )
		$title = "$title $sep " . sprintf( __( 'Page %s', 'twentytwelve' ), max( $paged, $page ) );

	return $title;
}
add_filter( 'wp_title', 'twentytwelve_wp_title', 10, 2 );

/**
 * Makes our wp_nav_menu() fallback -- wp_page_menu() -- show a home link.
 *
 * @since Twenty Twelve 1.0
 */
function twentytwelve_page_menu_args( $args ) {
	if ( ! isset( $args['show_home'] ) )
		$args['show_home'] = true;
	return $args;
}
add_filter( 'wp_page_menu_args', 'twentytwelve_page_menu_args' );

/**
 * Registers our main widget area and the front page widget areas.
 *
 * @since Twenty Twelve 1.0
 */
function twentytwelve_widgets_init() {
	register_sidebar( array(
		'name' => __( 'Main Sidebar', 'twentytwelve' ),
		'id' => 'sidebar-1',
		'description' => __( 'Appears on posts and pages except the optional Front Page template, which has its own widgets', 'twentytwelve' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget' => '</aside>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );

	register_sidebar( array(
		'name' => __( 'First Front Page Widget Area', 'twentytwelve' ),
		'id' => 'sidebar-2',
		'description' => __( 'Appears when using the optional Front Page template with a page set as Static Front Page', 'twentytwelve' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget' => '</aside>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );

	register_sidebar( array(
		'name' => __( 'Second Front Page Widget Area', 'twentytwelve' ),
		'id' => 'sidebar-3',
		'description' => __( 'Appears when using the optional Front Page template with a page set as Static Front Page', 'twentytwelve' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget' => '</aside>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );
}
add_action( 'widgets_init', 'twentytwelve_widgets_init' );

if ( ! function_exists( 'twentytwelve_content_nav' ) ) :
/**
 * Displays navigation to next/previous pages when applicable.
 *
 * @since Twenty Twelve 1.0
 */
function twentytwelve_content_nav( $html_id ) {
	global $wp_query;

	$html_id = esc_attr( $html_id );

	if ( $wp_query->max_num_pages > 1 ) : ?>
		<nav id="<?php echo $html_id; ?>" class="navigation" role="navigation">
			<h3 class="assistive-text"><?php _e( 'Post navigation', 'twentytwelve' ); ?></h3>
			<div class="nav-previous alignleft"><?php next_posts_link( __( '<span class="meta-nav">&larr;</span> Older posts', 'twentytwelve' ) ); ?></div>
			<div class="nav-next alignright"><?php previous_posts_link( __( 'Newer posts <span class="meta-nav">&rarr;</span>', 'twentytwelve' ) ); ?></div>
		</nav><!-- #<?php echo $html_id; ?> .navigation -->
	<?php endif;
}
endif;

if ( ! function_exists( 'twentytwelve_comment' ) ) :
/**
 * Template for comments and pingbacks.
 *
 * To override this walker in a child theme without modifying the comments template
 * simply create your own twentytwelve_comment(), and that function will be used instead.
 *
 * Used as a callback by wp_list_comments() for displaying the comments.
 *
 * @since Twenty Twelve 1.0
 */
function twentytwelve_comment( $comment, $args, $depth ) {
	$GLOBALS['comment'] = $comment;
	switch ( $comment->comment_type ) :
		case 'pingback' :
		case 'trackback' :
		// Display trackbacks differently than normal comments.
	?>
	<li <?php comment_class(); ?> id="comment-<?php comment_ID(); ?>">
		<p><?php _e( 'Pingback:', 'twentytwelve' ); ?> <?php comment_author_link(); ?> <?php edit_comment_link( __( '(Edit)', 'twentytwelve' ), '<span class="edit-link">', '</span>' ); ?></p>
	<?php
			break;
		default :
		// Proceed with normal comments.
		global $post;
	?>
	<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
		<article id="comment-<?php comment_ID(); ?>" class="comment">
			<header class="comment-meta comment-author vcard">
				<?php
					echo get_avatar( $comment, 44 );
					printf( '<cite class="fn">%1$s %2$s</cite>',
						get_comment_author_link(),
						// If current post author is also comment author, make it known visually.
						( $comment->user_id === $post->post_author ) ? '<span> ' . __( 'Post author', 'twentytwelve' ) . '</span>' : ''
					);
					printf( '<a href="%1$s"><time datetime="%2$s">%3$s</time></a>',
						esc_url( get_comment_link( $comment->comment_ID ) ),
						get_comment_time( 'c' ),
						/* translators: 1: date, 2: time */
						sprintf( __( '%1$s at %2$s', 'twentytwelve' ), get_comment_date(), get_comment_time() )
					);
				?>
			</header><!-- .comment-meta -->

			<?php if ( '0' == $comment->comment_approved ) : ?>
				<p class="comment-awaiting-moderation"><?php _e( 'Your comment is awaiting moderation.', 'twentytwelve' ); ?></p>
			<?php endif; ?>

			<section class="comment-content comment">
				<?php comment_text(); ?>
				<?php edit_comment_link( __( 'Edit', 'twentytwelve' ), '<p class="edit-link">', '</p>' ); ?>
			</section><!-- .comment-content -->

			<div class="reply">
				<?php comment_reply_link( array_merge( $args, array( 'reply_text' => __( 'Reply', 'twentytwelve' ), 'after' => ' <span>&darr;</span>', 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
			</div><!-- .reply -->
		</article><!-- #comment-## -->
	<?php
		break;
	endswitch; // end comment_type check
}
endif;

if ( ! function_exists( 'twentytwelve_entry_meta' ) ) :
/**
 * Prints HTML with meta information for current post: categories, tags, permalink, author, and date.
 *
 * Create your own twentytwelve_entry_meta() to override in a child theme.
 *
 * @since Twenty Twelve 1.0
 */
function twentytwelve_entry_meta() {
	// Translators: used between list items, there is a space after the comma.
	$categories_list = get_the_category_list( __( ', ', 'twentytwelve' ) );

	// Translators: used between list items, there is a space after the comma.
	$tag_list = get_the_tag_list( '', __( ', ', 'twentytwelve' ) );

	$date = sprintf( '<a href="%1$s" title="%2$s" rel="bookmark"><time class="entry-date" datetime="%3$s">%4$s</time></a>',
		esc_url( get_permalink() ),
		esc_attr( get_the_time() ),
		esc_attr( get_the_date( 'c' ) ),
		esc_html( get_the_date() )
	);

	$author = sprintf( '<span class="author vcard"><a class="url fn n" href="%1$s" title="%2$s" rel="author">%3$s</a></span>',
		esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
		esc_attr( sprintf( __( 'View all posts by %s', 'twentytwelve' ), get_the_author() ) ),
		get_the_author()
	);

	// Translators: 1 is category, 2 is tag, 3 is the date and 4 is the author's name.
	if ( $tag_list ) {
		$utility_text = __( 'This entry was posted in %1$s and tagged %2$s on %3$s<span class="by-author"> by %4$s</span>.', 'twentytwelve' );
	} elseif ( $categories_list ) {
		$utility_text = __( 'This entry was posted in %1$s on %3$s<span class="by-author"> by %4$s</span>.', 'twentytwelve' );
	} else {
		$utility_text = __( 'This entry was posted on %3$s<span class="by-author"> by %4$s</span>.', 'twentytwelve' );
	}

	printf(
		$utility_text,
		$categories_list,
		$tag_list,
		$date,
		$author
	);
}
endif;

/**
 * Extends the default WordPress body class to denote:
 * 1. Using a full-width layout, when no active widgets in the sidebar
 *    or full-width template.
 * 2. Front Page template: thumbnail in use and number of sidebars for
 *    widget areas.
 * 3. White or empty background color to change the layout and spacing.
 * 4. Custom fonts enabled.
 * 5. Single or multiple authors.
 *
 * @since Twenty Twelve 1.0
 *
 * @param array Existing class values.
 * @return array Filtered class values.
 */
function twentytwelve_body_class( $classes ) {
	$background_color = get_background_color();

	if ( ! is_active_sidebar( 'sidebar-1' ) || is_page_template( 'page-templates/full-width.php' ) )
		$classes[] = 'full-width';

	if ( is_page_template( 'page-templates/front-page.php' ) ) {
		$classes[] = 'template-front-page';
		if ( has_post_thumbnail() )
			$classes[] = 'has-post-thumbnail';
		if ( is_active_sidebar( 'sidebar-2' ) && is_active_sidebar( 'sidebar-3' ) )
			$classes[] = 'two-sidebars';
	}

	if ( empty( $background_color ) )
		$classes[] = 'custom-background-empty';
	elseif ( in_array( $background_color, array( 'fff', 'ffffff' ) ) )
		$classes[] = 'custom-background-white';

	// Enable custom font class only if the font CSS is queued to load.
	if ( wp_style_is( 'twentytwelve-fonts', 'queue' ) )
		$classes[] = 'custom-font-enabled';

	if ( ! is_multi_author() )
		$classes[] = 'single-author';

	return $classes;
}
add_filter( 'body_class', 'twentytwelve_body_class' );

/**
 * Adjusts content_width value for full-width and single image attachment
 * templates, and when there are no active widgets in the sidebar.
 *
 * @since Twenty Twelve 1.0
 */
function twentytwelve_content_width() {
	if ( is_page_template( 'page-templates/full-width.php' ) || is_attachment() || ! is_active_sidebar( 'sidebar-1' ) ) {
		global $content_width;
		$content_width = 960;
	}
}
add_action( 'template_redirect', 'twentytwelve_content_width' );

/**
 * Add postMessage support for site title and description for the Theme Customizer.
 *
 * @since Twenty Twelve 1.0
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 * @return void
 */
function twentytwelve_customize_register( $wp_customize ) {
	$wp_customize->get_setting( 'blogname' )->transport = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport = 'postMessage';
}
add_action( 'customize_register', 'twentytwelve_customize_register' );

/**
 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
 *
 * @since Twenty Twelve 1.0
 */
function twentytwelve_customize_preview_js() {
	wp_enqueue_script( 'twentytwelve-customizer', get_template_directory_uri() . '/js/theme-customizer.js', array( 'customize-preview' ), '20120827', true );
}
add_action( 'customize_preview_init', 'twentytwelve_customize_preview_js' );


/* Damao custom , Android 主题 */
function android_related()
{
    $posttags = get_the_tags();
    $postid = get_the_ID();
    if ($posttags) {
        foreach ($posttags as $tag) {
            query_posts('showposts=5&tag=' . $tag->name);
            while (have_posts()) : the_post();
                if ($postid != get_the_ID()) {
                    ?>
                    <li><a href="<?php the_permalink() ?>"
                           title="<?php the_title(); ?>"><?php the_title(); ?></a></li>
                <?php
                }
            endwhile;
            wp_reset_query();
            break;
        }
    }
}


//ajax-comments

add_action('comment_post', 'ajaxcomments_stop_for_ajax', 20, 2);
function ajaxcomments_stop_for_ajax($comment_ID, $comment_status)
{
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        //If AJAX Request Then
        switch ($comment_status) {
            case '0':
                //notify moderator of unapproved comment
                wp_notify_moderator($comment_ID);
            case '1': //Approved comment
                echo "success";
                $commentdata =& get_comment($comment_ID, ARRAY_A);
                $post =& get_post($commentdata['comment_post_ID']); //Notify post author of comment
                if (get_option('comments_notify') && $commentdata['comment_approved'] && $post->post_author != $commentdata['user_ID'])
                    wp_notify_postauthor($comment_ID, $commentdata['comment_type']);
                break;
            default:
                echo "error";
        }
        exit;
    }
}

//comment mail
function android_mailtocommente_get_email($comments){
    $temp =array();
    foreach ($comments as $comment){
        $name = $comment->comment_author;
        if(!array_key_exists($name,$temp)){
            $email = $comment->comment_author_email;
            $temp["$name"] = $email;
        }
    }
    return $temp;
}
function android_mailtocommenter_get_names($content){
    $content = preg_replace('/<a\shref="#comment-[0-9]+">/s','',$content);
    $content = preg_replace('/<a\shref="#comment-([0-9])+"\srel="nofollow">/s','',$content);
    $content = preg_replace('/<a\srel="nofollow"\shref="#comment-([0-9])+">/s','',$content);
    $names  = explode(' ',$content);
    $output = array();
    foreach($names as $name){
        $name = $name;
        $number = substr_count($name,'@');
        if ($number >0 ){
            $length = strlen($name);
            $pos = strrpos($name,'@')+1;
            $n = substr($name,$pos,$length);
            $output["$n"] = $n;
        }
    }
    return $output;
}
function android_mailtocommenter_filter($comment,$username){
    global $wpdb;

    $contents[0] = 'Your comment on [%blog_name%] just been replied by %comment_author%';
    $contents[1] ='Hello, %user%.<br/>Your comment on 《<a href="%post_link%">%post_title%</a>》just been replied by（%comment_author%）. Why not check it rightnow. ^_^<br/><div style="padding:5px;border:1px solid #888;">Your comment:<br />%your_comment%<div style="margin-left:5px;margin-right:5px;padding:5px;border:1px solid #ccc;">   New reply:<br />%reply_comment%<br /><div align="right">%comment_time%</div></div></div><div style="margin-top:10px;padding-bottom:10px;border-bottom:1px solid #ccc;"><a href="%comment_link%" target="_blank">View reply</a>, or click <a href="mailto:%admin_email%">here</a> to send mail to Admin</div><div align="right">DO Not reply this mail</div><a href="%blog_link%">%blog_name%</a>，Welcom to subscribe to <a href="%rss_link%">%rss_name%</a>.';
    $comment_id = $comment['comment_ID'];
    $post_id = $comment['comment_post_ID'];
    $post = get_post($post_id);
    $admin_email = get_option('admin_email');
    $blog_name = get_option('blogname');
    $blog_link = get_option('home');
    $comment_author = $comment['comment_author'];
    $post_link =  get_permalink($post_id);
    $comment_link = $post_link."#comment-$comment_id";
    $comment_time = $comment['comment_date'];
    $post_title =  $post->post_title;
    $reply_comment = $comment['comment_content'];
    $your_comment = $wpdb->get_var("SELECT $wpdb->comments.comment_content FROM $wpdb->comments WHERE $wpdb->comments.comment_post_ID='$post_id' AND $wpdb->comments.comment_author='$username' ORDER BY $wpdb->comments.comment_date DESC");
    $index = 0;
    foreach ($contents as $content){
        $filter = $content;
        $filter= str_replace("%admin_email%",$admin_email,$filter);
        $filter= str_replace("%blog_name%",$blog_name,$filter);
        $filter= str_replace("%blog_link%",$blog_link,$filter);
        $filter= str_replace("%comment_author%",$comment_author,$filter);
        $filter= str_replace("%comment_link%",$comment_link,$filter);
        $filter= str_replace("%comment_time%",$comment_time,$filter);
        $filter= str_replace("%your_comment%",$your_comment,$filter);
        $filter= str_replace("%post_link%",$post_link,$filter);
        $filter= str_replace("%post_title%",$post_title,$filter);
        $filter= str_replace("%reply_comment%",$reply_comment,$filter);
        $filter= str_replace("%rss_name%","RSS",$filter);
        $filter= str_replace("%rss_link%",get_bloginfo_rss('rss2_url'),$filter);
        $filter= str_replace("%user%",$username,$filter);
        $output[$index]= $filter;
        $index++;
    }
    return $output;
}
function android_mailtocommenter_send_email($to,$subject,$message){
    $blogname = get_option('blogname');
    $charset = get_option('blog_charset');
    $headers  = "From: $blogname \n" ;
    $headers .= "MIME-Version: 1.0\n";
    $headers .= "Content-Type: text/html;charset=\"$charset\"\n";
    $to = strtolower($to);
    return @wp_mail($to, $subject, $message, $headers);
}
function android_mailtocommenter($cid){
    global $wpdb;
    $cid = (int)$cid;
    $commentdata = get_commentdata($cid,1,false);
    $owner_email = $commentdata['comment_author_email'];
    $post_id = (int)$commentdata['comment_post_ID'];
    $comments = get_approved_comments($post_id);
    $commentcontent = $commentdata['comment_content'];
    $output = android_mailtocommenter_get_names($commentcontent);
    if (!$output) return;
    $mails = android_mailtocommente_get_email($comments);
    $n = array();
    $admin_email = get_option('admin_email');
    $result = 0;
    foreach ($output as $name){
        if ((array_key_exists($name,$mails)) and ($mails["$name"]!=$owner_email)){
            $to = $mails["$name"];
            $filter = android_mailtocommenter_filter($commentdata,$name);
            $subject =$filter[0];
            $message = $filter[1];
            $message = apply_filters('comment_text', $message);
            if(android_mailtocommenter_send_email($to,$subject,$message)){
                $result++;
            }
            $n["$name"] = $name;
        }
    }

    if ($result>0){
        $subject = "CC. $subject";
        $n = implode(',',$n);
        $n = "<br/>This comment has been sent to {$n}.<br/>";
        $m = $n.'Backup copy sent to admin<br/>'.$message;
        $to = strtolower(get_option('admin_email'));
        android_mailtocommenter_send_email($to,$subject,$m);
    }
}
add_action('comment_post', create_function('$cid', 'return android_mailtocommenter($cid);'));



/**
 * 判断是否移动终端
 */

function is_mobile()
{
    if (stristr($_SERVER['HTTP_USER_AGENT'], 'android') || stristr($_SERVER['HTTP_USER_AGENT'], 'iphone')) {
        return true;
    } else {
        return false;
    }
}

function article_nav($content)
{
    /**
     * 文章目录
     */

    $matches = array();
    $ul_li = '';

    $r = "/<h2>(.*?)<\\/h2>/im";

    if (preg_match_all($r, $content, $matches)) {
        foreach ($matches[1] as $num => $title) {
            $content = str_replace($matches[0][$num], '<h2 id="article_nav_' . $num . '">' . $title . '</h2>', $content);
            $ul_li .= '<li><a href="#article_nav_' . $num . '" title="' . $title . '">' . $title . "</a></li>\n";
        }
        if (is_singular()) {
            $content = '<ul id="fn_article_nav"><li><a href="#top">返回顶部</a></li>'
                . $ul_li . '</ul>' . $content;
        }
    }


    return $content;
}

add_filter("the_content", "article_nav");
