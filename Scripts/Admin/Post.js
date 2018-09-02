;(function($, window){
    var editor;
    $(function(){
        initEditor();
        initEvent();
    });

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
            def_save.done(function(rep_data, textStatus, jqXHR){
                alert(rep_data["msg"]);
            }).fail(function(jqXHR, textStauts, err){
                alert(jqXHR.resonseJSON.msg);
            });
        });
        $("#btnPreview").on("click", function(){ 
            $("#hidStatus").val("2");
            $("form").submit();
            def_save.done(function(rep_data, textStatus, jqXHR){
                window.open("/post/" + rep_data["data"]["id"]);
            }).fail(function(jqXHR, textStatus, err){
                alert(jqXHR.responseJSON.msg);
            });
        });
    }

    function save() {
        return $.ajax({"url": "/admin/posts", "method": "post", "dataType": "json", "data": $("form").serialize()});
    }
}(jQuery, window));
