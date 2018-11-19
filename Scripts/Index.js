;(function($, window){
    $(function(){
        initEvent();
    });

    function initEvent(){
        setLoadMoreEvent();
    }
    function setLoadMoreEvent() {
        $(window).on("scroll", function(){
            if($("#articles article:last").offset().top <= ($(window).scrollTop() + $(window).innerHeight())) {
                $(window).off("scroll");
                if($("#load_more").length == 0) {
                    $("#articles article:last").append("<div id=\"load_more\">加载中……</div>");
                }
                var start_time = $("#articles article:last").children("h4").text();
                var count = 10;//默认每次加载数量

                getPosts({"start_lt_time": start_time, "count": 1 + count}).done(function(data, textStatus, jqXHR) {
                    var length = data["data"].length;
                    bindPosts(data["data"], (length == count ? length - 1 : length));
                    if(length == count) {
                        setLoadMoreEvent();
                    }
                }).fail(function(){
                    $("#load_more").text("没有更多……");
                }).always(function() {
                    $("#load_more").hide("slow", function(){
                        $("#load_more").remove();
                    });
                });
            }
        });
    }
    
    function bindPosts(data, length) {
        for(var i = 0; i < length; i++) {
            $("#articles").append("<article></article>");
            $("#articles article").last().append("<h4><time pubdate=" + data[i]["gmt_modify"] + ">" + data[i]["gmt_modify"] + "</time></h4>");
            $("#articles article").last().append("<h1><a href=\"post/" + data[i]["id"] + "\">" + data[i]["title"] + "</a></h4>");
            $("#articles article").last().append($.trim(data[i]["subtitle"]) != "" ? "<div>" + data[i]["subtitle"] + "</div>" : "");
            $("#articles article").last().append($.trim(data[i]["foreword"]) != "" ? "<div>" + data[i]["foreword"] + "</div>" : "");
        }
    }
    function getPosts(data) {
        return $.ajax({"url": "", "dataType": "json", "method": "get", "data": data});
    }
}(jQuery, window));
