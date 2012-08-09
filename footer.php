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
    $("#btn-quicknav").toggle(function () {
            $("#header-wrap").addClass("quicknav");
            $("#quicknav").slideDown();
        },
        function () {
            $("#header-wrap").removeClass("quicknav");
            $("#quicknav").slideUp();
        }
    );
    $("#search-container").mouseover(function () {
        $(this).addClass("active");
        $("#s").focus().blur(function () {
            $("#search-container").removeClass("active");
        });
    });

    //smart-nav
    if ($("#side-nav").length > 0) {
        var side_nav_top = $("#side-nav").offset().top; // gotop button height
        $(document).scroll(function () {
            if ($(this).scrollTop() > side_nav_top) {
                $("#devdoc-nav").addClass("scroll-pane")
            } else {
                $("#devdoc-nav").removeClass("scroll-pane")
            }
        });
        $("#smart-nav").prepend($("#smart-nav-containter").text());

        $(".nav-section-header").click(function () {
            if ($(this).parent().hasClass("expanded")) {
                $(".nav-section").removeClass("expanded");
                $(".nav-section ul").slideUp();
            } else {
                $(".nav-section").removeClass("expanded");
                $(this).parent().addClass("expanded");
                $(".nav-section ul").slideUp();
                $(this).siblings("ul").stop().slideDown();
            }
        });
        if (!$(".nav-section").eq(0).find("li").size()) {
            $(".nav-section").eq(0).remove();
        }
        $(".nav-section-header").eq(0).click();
        if ($("#smart-nav-related li").size()) {
            $("#smart-nav-related").fadeIn()
        }
        $("#respond input,#respond textarea").attr("placeholder", function () {
            return $(this).siblings("label").text()
        });
    }
    //search
    function makeAjaxSearch(result) {
        if (result.length == 0) {
            $("#search_filtered").empty().show().append('<li><a href="javascript:vold(0)"><strong>404</strong></a></li>');
        } else {
            $("#search_filtered").empty().show();
            for (var i = 0; i < result.length - 1; i++) $("#search_filtered").append('<li><a href="' + result[i][1] + '">' + result[i][0] + '</a></li>');
        }
    }
    var delaySearch;
    function startSearch() {
        $.ajax({
            type:"GET",
            url:"<?php echo esc_url(home_url('/')); ?>",
            data:"ajax=1&s=" + $("#s").val(),
            dataType:'json',
            success:function (result) {
                makeAjaxSearch(result);
            }
        });
    }
    $("#s").keyup(function () {
        if ($("#s").val() != "") {
            if (delaySearch) {
                clearTimeout(delaySearch)
            }
            delaySearch = setTimeout(startSearch, 250);
        } else $("#search_filtered").fadeOut();
    });
</script>
</body>
</html>