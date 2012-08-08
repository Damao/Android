<?php
/**
 * The template for displaying search forms in Android
 *
 * @package WordPress
 * @subpackage Twenty_Eleven
 * @since Android 1.0
 */
?>

<div class="fn_search" id="search-container">
    <div class="search-inner">
        <div id="search-btn"></div>
        <div class="left"></div>
        <form method="get" id="searchform" action="<?php echo esc_url(home_url('/')); ?>">
            <input type="text" value="" autocomplete="off" placeholder="<?php esc_attr_e('Search', 'android'); ?>" name="s" id="s" />
            <input type="submit" class="submit" name="submit" id="searchsubmit" value="<?php esc_attr_e('Search', 'android'); ?>"/>
        </form>
        <div class="right"></div>
        <a class="close ui-hide">close</a>

        <div class="left"></div>
        <div class="right"></div>
    </div>
</div>
<div id="search_filtered_wrapper">
    <div id="search_filtered_div" class="no-display">
        <ul id="search_filtered">
        </ul>
    </div>
</div>