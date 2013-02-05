var input_search, commentform, is_admin_bar, fn_article_nav_right, fn_article_nav_top, secondary_top, secondary_left, secondary_height, colophon_height;
function init() {
    input_search = $("#s");
    commentform = $('#commentform');
    is_admin_bar = $('body').hasClass('admin-bar');
    fn_article_nav_right = $(window).width() - ($('#content').offset().left + $('#content').outerWidth());
    if ($('#fn_article_nav')[0]) {
        fn_article_nav_top = $('body').hasClass('admin-bar') ? $('#fn_article_nav').offset().top - 28 : $('#fn_article_nav').offset().top;
    }
    secondary_top = $('#secondary').offset().top;
    secondary_left = $('#secondary').offset().left;
    secondary_height = $('#secondary').height();
    colophon_height = $(document).height() - $('#colophon').height() - $('#colophon').offset().top;
}
init();
$('window').resize(function () {
    init();
});

$('#comment').focus(function () {
    $('.form-allowed-tags').slideDown();
});

$("#btn-quicknav").toggle(function () {
        $("#header-wrap").addClass("quicknav");
        $("#quicknav").slideDown();
    },
    function () {
        $("#header-wrap").removeClass("quicknav");
        $("#quicknav").slideUp();
    }
);
function searchActive() {
    $("#search-container").addClass("active");
    input_search.focus().blur(function () {
        $("#search-container").removeClass("active");
    });
}
$("#search-container").mouseover(function () {
    searchActive();
});
function keydownSearch(e) {
    if (e.keyCode == 191) {
        e.preventDefault();
        searchActive();
    }
}
$(document).bind("keydown.search", function (e) {
    keydownSearch(e)
});
$("input,textarea").focus(function () {
    $(document).unbind("keydown.search");
}).blur(function () {
        $(document).bind("keydown.search", function (e) {
            keydownSearch(e)
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
        $("#search_filtered").empty().show().append('<li><a href="javascript:vold(0)"><strong>神马也木有,呵呵</strong></a></li>');
    } else {
        $("#search_filtered").empty().show();
        for (var i = 0; i < result.length - 1; i++) $("#search_filtered").append('<li><a href="' + result[i]["url"] + '">' + result[i]["title"] + '</a></li>');
    }
}
var delaySearch;

function startSearch() {
    $.ajax({
        type: "GET",
        url: home_url, //这玩意儿来自php,囧
        data: "s=" + input_search.val(),
        dataType: 'json',
        success: function (result) {
            makeAjaxSearch(result);
            console.log(result);
        }
    });
}
var event_ajax_search = {
    bind_event: function () {
        input_search.bind('keyup', function (e) {
            if (input_search.val() != "" && e.keyCode != 40) {
                if (delaySearch) {
                    clearTimeout(delaySearch)
                }
                delaySearch = setTimeout(startSearch, 200);
            }
            if (e.keyCode == 40) {
                search_filtered.moveable();
            }
        })
    },
    unbind_event: function () {
        input_search.unbind('keyup');
    }
};
var search_filtered = {
    moveable: function () {
        var current = 0;
        $('#search_filtered').find('a').eq(current).focus();
        $(document).bind("keydown.search_result", function (e) {
            if (e.keyCode == 40) {

                if (current >= $('#search_filtered').find('a').size()) {
                    current = 0;
                }

                $('#search_filtered').find('a').eq(++current).focus();
                e.preventDefault();

            }
            if (e.keyCode == 38) {
                if (current < 0) {
                    current = $('#search_filtered').find('a').size() - 1;
                }

                $('#search_filtered').find('a').eq(--current).focus();
                e.preventDefault();
            }
        });
    },
    hide: function () {
        $(document).unbind("keyup.search_result");
        $('#search_filtered').fadeOut();
    }
};
input_search.focus(function () {
    event_ajax_search.bind_event();
}).blur(function () {
        event_ajax_search.unbind_event();
    });


//ajax comments
commentform.prepend('<div id="ajax-comment-info" ></div>');
commentform.submit(function () {
    var infodiv = $('#ajax-comment-info');
    infodiv.fadeIn();
    //serialize and store form data in a variable
    var formdata = commentform.serialize();
    //Add a status message
    infodiv.html('<div class="ajax-progress">真的是AJAX提交数据中... ... 请耐心等待</div>');
    //Extract action URL from commentform
    var formurl = commentform.attr('action');
    //Post Form with data
    $.ajax({
        type: 'post',
        url: formurl,
        data: formdata,
        error: function (XMLHttpRequest, textStatus, errorThrown) {
            infodiv.html('<div class="ajax-error" >' + XMLHttpRequest.responseText.match(/<p>(.*?)<\/p>/g) + '<br>textStatus:' + textStatus + '<br>errorThrown:' + errorThrown + '</div>');
        },
        success: function (data, textStatus) {
            if (data == "success") {
                infodiv.html('<div class="ajax-success" >评论成功. textStatus:' + textStatus + '</div>');
                $("#respond").before('<ul class="children"> <li class="comment"> <article class="comment"> <footer> <div class="comment-author vcard"> <strong>You</strong> <span class="says">said:</span></div> </footer> <div class="comment-content"><p>' + commentform.find('textarea[name=comment]').val() + '</p> </div></article> </li> </ul> ');
                commentform.find('textarea[name=comment]').val('');
                setTimeout(function () {
                    $("#ajax-comment-info").fadeOut();
                }, 3000);
            } else {
                infodiv.html('<div class="ajax-error" >服务器脑瘫,八成被 Akismet 君跳大,请歇会儿再试.</div>');
            }
        }
    });
    return false;
});


//thread comment
function moveEnd(id) {
    var obj = document.getElementById(id);
    obj.focus();
    var len = obj.value.length;
    if (document.selection) {
        var sel = obj.createTextRange();
        sel.moveStart('character', len);
        sel.collapse();
        sel.select();
    } else if (typeof obj.selectionStart == 'number' && typeof obj.selectionEnd == 'number') {
        obj.selectionStart = obj.selectionEnd = len;
    }
}
addComment = {moveForm: function (d, f, i, c) {
    var m = this, a, h = m.I(d), b = m.I(i), l = m.I("cancel-comment-reply-link"), j = m.I("comment_parent"), k = m.I("comment_post_ID");
    if (!h || !b || !l || !j) {
        return
    }
    m.respondId = i;
    c = c || false;
    if (!m.I("wp-temp-form-div")) {
        a = document.createElement("div");
        a.id = "wp-temp-form-div";
        a.style.display = "none";
        b.parentNode.insertBefore(a, b)
    }
    h.parentNode.insertBefore(b, h.nextSibling);
    if (k && c) {
        k.value = c
    }
    j.value = f;
    l.style.display = "";
    l.onclick = function () {
        var n = addComment, e = n.I("wp-temp-form-div"), o = n.I(n.respondId);
        if (!e || !o) {
            return
        }
        n.I("comment_parent").value = "0";
        e.parentNode.insertBefore(o, e);
        e.parentNode.removeChild(e);
        this.style.display = "none";
        this.onclick = null;
        return false
    };
    try {
        if ($("#comment").val() == "" || /@/.test($("#comment").val())) {//切换用户的时候@人也要变一下...
            $("#comment").val('@' + $("#" + d).find(".fn").eq(0).text() + ' ');
        }
        moveEnd("comment");
    } catch (g) {
    }
    return false
}, I: function (a) {
    return document.getElementById(a)
}};


$(".morehover").hover(function () {
    $(this).toggleClass("hover")
});


$(document).scroll(function () {
    if ($(this).scrollTop() > fn_article_nav_top) {
        if (is_admin_bar) {
            $('#fn_article_nav').css({'position': 'fixed', 'right': fn_article_nav_right, 'top': '38px'});
        } else {
            $('#fn_article_nav').css({'position': 'fixed', 'right': fn_article_nav_right, 'top': '10px'});
        }
    } else {
        $('#fn_article_nav').css({'position': 'absolute', 'right': 0, 'top': '10px'});
    }
    if ($(this).scrollTop() + $(window).height() > secondary_top + secondary_height) {
        $('#secondary').css({
            'position': 'fixed',
            'left': secondary_left,
            'top': $(window).height() - secondary_height - colophon_height
        })
    } else {
        $('#secondary').css({
            'position': 'relative',
            'left': 0,
            'top': 0
        })
    }
});
