;(function($, window){
    $(function(){
        initEvent();
        initData();
    });

    function initEvent(){
        $("form").on("submit", function() {
            initData();
            return false;
        });
    }
    function initData(){
        get().done(function(rep_data, textStatus, jqXHR) {
            $("#tbList tbody").empty();
            bindData(rep_data["data"]);
        });
    }

    function bindData(data) {
        var tb = $("#tbList");
        for(var i = 0, l = data.length; i < l; i++) {
            $("#tbList").append("<tr></tr>");

            $("#tbList tr:last").append("<td>" + data[i]["title"] + "</td>");
            $("#tbList tr:last").append("<td>" + (data[i]["status"] == 0 ? "禁用" : (data[i]["status"] == 1 ? "正常" : "草稿")) + "</td>");
            $("#tbList tr:last").append("<td><a href=\"./post/" + data[i]["id"] + "/" + data[i]["status"] + "\">编辑</a></td>");
        }
    }
    function get(){
        return $.ajax({
            "url": "./posts", "dataType": "json", "method": "get", "data": $("form").serialize()
        });
    }
}(jQuery, window));
