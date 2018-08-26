;(function($, window){
    $(function(){
        initData();
    });

    function initEvent(){
        $("form").on("submit", function(){
            initData();
            return false;
        });
    }
    function initData(){
        get().done(function(rep_data, textStatus, jqXHR){
            bindData(rep_data["data"]);
        });
    }

    function bindData(data) {
        var tb = $("#tbList");
        for(var i = 0, l = data.length; i < l; i++) {
            $("#tbList").append("<tr><td>" + data[i]["title"] + "</td><td>" + data[i]["status"] + "</td></tr>");
        }
    }
    function get(){
        return $.ajax({
            "url": "./posts", "dataType": "json", "method": "get", "data": {"columns": "id,title,status"}
        });
    }
}(jQuery, window));
