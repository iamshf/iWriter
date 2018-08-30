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
                "width": "100%"
            });
        });
    }
    function initEvent(){
        $("form").on("submit", function(){
            editor.sync();
            save().done(function(rep_data, textStatus, jqXHR){
                alert(jqXHR.responseText);
            }).fail(function(jqXHR, textStatus, err){
                alert(jqXHR.responseText);
            });
            return false;
        });
        $("#btnSubmit").on("click", function(){ 
            $("#hidStatus").val("1");
        });
        $("#btnPreview").on("click", function(){ 
            $("#hidStatus").val("2");
            return $("form").submit();
        });
    }

    function getCategorys(){
        return $.ajax({"url": "./category", "method": "get", "dataType": "json"});
    }
    function save() {
        return $.ajax({"url": "./posts", "method": "post", "dataType": "json", "data": $("form").serialize()});
    }
}(jQuery, window));
