;(function($, window){
    $(function(){
        initData();
        initEvent();
    });

    function initData(){
        getPosts().done(function(rep_data, textStatus, jqXHR){
            bindPosts(rep_data["data"]);
        });
        getCategories().done(function(data, textStatus, jqXHR){
            bindCategories(data["data"]);
        });
    }
    function initEvent(){
        $("#main").on("click", "#categories a", function(){
            getPosts({"category_id": $(this).data("item")["id"]}).done(function(rep_data, textStatus, jqXHR) {
                $("#articles").empty();
                bindPosts(rep_data["data"]);
            });
        });
    }
    
    function bindPosts(data) {
        for(var i = 0, l = data.length; i < l; i++) {
            $("#articles").append("<article></article>");
            $("#articles article").last().append("<h4>" + data[i]["gmt_modify"] + "</h4>");
            $("#articles article").last().append("<h1><a href=\"post/" + data[i]["id"] + "\">" + data[i]["title"] + "</a></h4>");
            $("#articles article").last().append($.trim(data[i]["subtitle"]) != "" ? "<div>" + data[i]["subtitle"] + "</div>" : "");
            $("#articles article").last().append($.trim(data[i]["foreword"]) != "" ? "<div>" + data[i]["foreword"] + "</div>" : "");
        }
    }
    function bindCategories(data) {
        $("#main").append("<nav id=\"categories\"></nav>");
        for(var i = 0, l = data.length; i < l; i++) {
            var category = data[i];

            $("#categories").append("<a></a>");
            var last = $("#categories a:last");

            for(var x = 1; x < category["deep"]; x++) {
                last.append("&nbsp;&nbsp;&nbsp;&nbsp;");
            }
            last.append(category["name"]);
            last.data("item", category);
        }
    }
    function getPosts(data) {
        return $.ajax({"url": "", "dataType": "json", "method": "get", "data": data});
    }
    function getCategories(){
        return $.ajax({"url": "./admin/category", "dataType": "json", "method": "get"});
    }
}(jQuery, window));
