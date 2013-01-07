<?php
$is_ad = false; // 庙的广告,默认关闭

function console($log){
    echo <<<EOF
<script>console.log("$log")</script>
EOF;
}

function ad_header(){
    global $is_ad;
    if($is_ad){
        echo <<<EOF
            <link rel="author" href="https://plus.google.com/116860638371403168572" />
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
EOF;
    }
}



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

    $r = "/<h2>(.*?)<\\/h2>/im";

    if (preg_match_all($r, $content, $matches)) {
        foreach ($matches[1] as $num => $title) {
            $content = str_replace($matches[0][$num], '<h2 id="article_nav-' . $num . '">' . $title . '</h2>', $content);
            $ul_li .= '<li><a href="#article_nav-' . $num . '" title="' . $title . '">' . $title . "</a></li>\n";
        }
        if (is_singular()) {
            $content = '<textarea id="smart-nav-containter" class="ui-hide"><li id="smart-nav-recent" class="nav-section"><div class="nav-section-header"><a href="javascript:vold(0)">Article Nav</a></div><ul>'
                . $ul_li . '<li><a href="#respond">发表评论</a></li></ul></li></textarea>' . $content;
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


