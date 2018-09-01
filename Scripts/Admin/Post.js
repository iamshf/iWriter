;(function($, window){
    var editor;
    $(function(){
        bindCategorys();
        initEditor();
        initEvent();
    });
    function bindCategorys(){
        getCategorys().done(function(data, textStatus, jqXHR){
            $("form fieldset:last").append("<ul id=\"categorys\"></ul>");
            for(var i = 0, l = data["data"].length; i < l; i++) {
                var category = data["data"][i];

                $("#categorys").append("<li></li>");
                var last = $("#categorys li:last");

                for(var x = 1; x < category["deep"]; x++) {
                    last.append("&nbsp;&nbsp;&nbsp;&nbsp;");
                }
                last.append("<input type=\"checkbox\" id=\"category_" + category["id"] + "\" name=\"category_ids[]\" value=\"" + category["id"] + "\" />");
                last.append("<label for=\"category_" + category["id"] + "\">" + category["name"] + "</label>");
            }
        });
    }

    function initEditor(){
        KindEditor.ready(function(K) {
            editor = K.create("#content", {
                "width": "100%",
                "uploadJson": "./upload",
                "filePostName": "file_info",
                "allowFileManager": true,
                "fileManagerJson": "./uploadFiles"
            });
        });
    }
    function initEvent(){
        var def_save;
        $("form").on("submit", function() {
            editor.sync();
            def_save = save();
            return false;
        });
        $("#btnSubmit").on("click", function(){ 
            $("#hidStatus").val("1");
            def_save.always(function(rep_data, textStatus, jqXHR){
                alert(rep_data["msg"]);
            });
        });
        $("#btnPreview").on("click", function(){ 
            $("#hidStatus").val("2");
            $("form").submit();
            def_save.done(function(rep_data, textStatus, jqXHR){
                window.open("/post/" + rep_data["data"]["id"]);
            }).fail(function(jqXHR, textStatus, err){
                alert(jqXHR.responseText);
            });
        });
    }

    function getCategorys(){
        return $.ajax({"url": "./category", "method": "get", "dataType": "json"});
    }
    function save() {
        return $.ajax({"url": "./posts", "method": "post", "dataType": "json", "data": $("form").serialize()});
    }
}(jQuery, window));
