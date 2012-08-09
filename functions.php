<?php



if (!function_exists('android_content_nav')
) :
    /**
     * Display navigation to next/previous pages when applicable
     */
    function android_content_nav($nav_id)
    {
        global $wp_query;

        if ($wp_query->max_num_pages > 1) : ?>
        <nav id="<?php echo $nav_id; ?>">
            <h3 class="assistive-text"><?php _e('Post navigation', 'android'); ?></h3>

            <div
                class="nav-previous"><?php next_posts_link(__('<span class="meta-nav">&larr;</span> Older posts', 'android')); ?></div>
            <div
                class="nav-next"><?php previous_posts_link(__('Newer posts <span class="meta-nav">&rarr;</span>', 'android')); ?></div>
        </nav><!-- #nav-above -->
        <?php endif;
    }
endif; // android_content_nav

/**
 * Return the URL for the first link found in the post content.
 *
 * @since Android 1.0
 * @return string|bool URL or false when no link is present.
 */
function android_url_grabber()
{
    if (!preg_match('/<a\s[^>]*?href=[\'"](.+?)[\'"]/is', get_the_content(), $matches))
        return false;

    return esc_url_raw($matches[1]);
}


if (!function_exists('android_comment')
) :
    /**
     * Template for comments and pingbacks.
     *
     * To override this walker in a child theme without modifying the comments template
     * simply create your own android_comment(), and that function will be used instead.
     *
     * Used as a callback by wp_list_comments() for displaying the comments.
     *
     * @since Android 1.0
     */
    function android_comment($comment, $args, $depth)
    {
        $GLOBALS['comment'] = $comment;
        switch ($comment->comment_type) :
            case 'pingback' :
            case 'trackback' :
                ?>
	<li class="post pingback">
		<p><?php _e('Pingback:', 'android'); ?> <?php comment_author_link(); ?><?php edit_comment_link(__('Edit', 'android'), '<span class="edit-link">', '</span>'); ?></p>
                    <?php
                break;
            default :
                ?>
                <li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
                    <article id="comment-<?php comment_ID(); ?>" class="comment">
                        <footer class="comment-meta">
                            <div class="comment-author vcard">
                                <?php
                                $avatar_size = 68;
                                if ('0' != $comment->comment_parent)
                                    $avatar_size = 39;

                                echo get_avatar($comment, $avatar_size);

                                /* translators: 1: comment author, 2: date and time */
                                printf(__('%1$s on %2$s <span class="says">said:</span>', 'android'),
                                    sprintf('<span class="fn">%s</span>', get_comment_author_link()),
                                    sprintf('<a href="%1$s"><time pubdate datetime="%2$s">%3$s</time></a>',
                                        esc_url(get_comment_link($comment->comment_ID)),
                                        get_comment_time('c'),
                                        /* translators: 1: date, 2: time */
                                        sprintf(__('%1$s at %2$s', 'android'), get_comment_date(), get_comment_time())
                                    )
                                );
                                ?>

                                <?php edit_comment_link(__('Edit', 'android'), '<span class="edit-link">', '</span>'); ?>
                            </div>
                            <!-- .comment-author .vcard -->

                            <?php if ($comment->comment_approved == '0') : ?>
                            <em class="comment-awaiting-moderation"><?php _e('Your comment is awaiting moderation.', 'android'); ?></em>
                            <br/>
                            <?php endif; ?>

                        </footer>

                        <div class="comment-content"><?php comment_text(); ?></div>

                        <div class="reply">
                            <?php comment_reply_link(array_merge($args, array('reply_text' => __('Reply <span>&darr;</span>', 'android'), 'depth' => $depth, 'max_depth' => $args['max_depth']))); ?>
                        </div>
                        <!-- .reply -->
                    </article>
                    <!-- #comment-## -->

                    <?php
                break;
        endswitch;
    }
endif; // ends check for android_comment()

if (!function_exists('android_posted_on')
) :
    /**
     * Prints HTML with meta information for the current post-date/time and author.
     * Create your own android_posted_on to override in a child theme
     *
     * @since Android 1.0
     */
    function android_posted_on()
    {
        printf(__('<span class="sep">Posted on </span><time class="entry-date" datetime="%3$s" pubdate>%4$s</time><span class="by-author"> <span class="sep"> by </span> <span class="author vcard"><a class="url fn n" href="%5$s" title="%6$s" rel="author">%7$s</a></span></span>', 'android'),
            esc_url(get_permalink()),
            esc_attr(get_the_time()),
            esc_attr(get_the_date('c')),
            esc_html(get_the_date()),
            esc_url(get_author_posts_url(get_the_author_meta('ID'))),
            esc_attr(sprintf(__('View all posts by %s', 'android'), get_the_author())),
            get_the_author()
        );
    }
endif;

/**
 * Adds two classes to the array of body classes.
 * The first is if the site has only had one author with published posts.
 * The second is if a singular post being displayed
 *
 * @since Android 1.0
 */
function android_body_classes($classes)
{

    if (function_exists('is_multi_author') && !is_multi_author())
        $classes[] = 'single-author';

    if (is_singular() && !is_home() && !is_page_template('showcase.php') && !is_page_template('sidebar-page.php'))
        $classes[] = 'singular';

    return $classes;
}

add_filter('body_class', 'android_body_classes');

function article_nav($content)
{
    /**
     * 文章目录
     */

    $matches = array();
    $ul_li = '';

    $r = "/<h2>([^<]+)<\/h2>/im";

    if (preg_match_all($r, $content, $matches)) {
        foreach ($matches[1] as $num => $title) {
            $content = str_replace($matches[0][$num], '<h2 id="article_nav-' . $num . '">' . $title . '</h2>', $content);
            $ul_li .= '<li><a href="#article_nav-' . $num . '" title="' . $title . '">' . $title . "</a></li>\n";
        }
        if (is_singular()) {
            $content = '<textarea id="smart-nav-containter" class="ui-hide"><li id="smart-nav-recent" class="nav-section"><div class="nav-section-header"><a href="javascript:vold(0)">Article Nav</a></div><ul>'
                . $ul_li . '</ul></li></textarea>' . $content;
        }
    }


    return $content;
}

add_filter("the_content", "article_nav");


// A trim function to remove the last character of a utf-8 string
// by following instructions on http://en.wikipedia.org/wiki/UTF-8
// dotann

function utf8_trim($str)
{
    for ($i = strlen($str) - 1; $i >= 0; $i -= 1) {
        $hex .= ' ' . ord($str[$i]);
        $ch = ord($str[$i]);
        if (($ch & 128) == 0) return (substr($str, 0, $i));
        if (($ch & 192) == 192) return (substr($str, 0, $i));
    }
    return ($str . $hex);
}

function android_excerpt($excerpt)
{
    $tmp_excerpt = substr($excerpt, 0, 255);
    return utf8_trim($tmp_excerpt) . '... ';
}

add_filter('the_excerpt', 'android_excerpt');
add_filter('the_excerpt_rss', 'android_excerpt');


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

add_action('comment_post', 'ajaxcomments_stop_for_ajax',20, 2);
function ajaxcomments_stop_for_ajax($comment_ID, $comment_status){
    if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){
        //If AJAX Request Then
        switch($comment_status){
            case '0':
                //notify moderator of unapproved comment
                wp_notify_moderator($comment_ID);
            case '1': //Approved comment
                echo "success";
                $commentdata=&get_comment($comment_ID, ARRAY_A);
                $post=&get_post($commentdata['comment_post_ID']); //Notify post author of comment
                if ( get_option('comments_notify') && $commentdata['comment_approved'] && $post->post_author != $commentdata['user_ID'] )
                    wp_notify_postauthor($comment_ID, $commentdata['comment_type']);
                break;
            default:
                echo "error";
        }
        exit;
    }
}
