;(function($, window){
    $(function(){
        initData();
    });

    function initData(){
        getPosts().done(function(rep_data, textStatus, jqXHR){
            bindPosts(rep_data["data"]);
        });
    }
    function bindPosts(data) {
        for(var i = 0, l = data.length; i < l; i++) {
            $("#main").append("<article></article>");
            $("#main article").first().append("<h4>" + data[i]["gmt_modify"] + "</h4>");
            $("#main article").first().append("<h1><a href=\"post/" + data[i]["id"] + "\">" + data[i]["title"] + "</a></h4>");
            $("#main article").first().append("<div>" + data[i]["subtitle"] + "</div>");
            $("#main article").first().append("<div>" + data[i]["foreword"] + "</div>");
        }
    }
    function getPosts(){
        return $.ajax({
            "url": "", "dataType": "json", "method": "get"
        });
    }
}(jQuery, window));
