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
    function initEvent() {
        $("form").on("submit", function(e) {
            e.preventDefault();
            editor.sync();
            save().done(function(data, textStatus, jqXHR){
                alert(data["msg"]);
                jqXHR.status == 201 && $("#id").val(data["data"]["id"]);
            }).fail(function(jqXHR, textStauts, err){
                alert(jqXHR.responseJSON.msg);
            });
        });
        $("#btnSubmit").on("click", function(){ 
            $("#hidStatus").val("1");
        });
        $("#btnPreview,#btnSave").on("click", function(){ 
            var _this = $(this);
            if($("form")[0].reportValidity()) {
                $("#hidStatus").val("2");
                editor.sync();
                save().done(function(data, textStatus, jqXHR){
                    jqXHR.status == 201 && $("#id").val(data["data"]["id"]);
                    _this.attr("id") == "btnPreview" && window.open("/post/" + data["data"]["id"]);
                }).fail(function(jqXHR, textStatus, err){
                    alert(jqXHR.responseJSON.msg);
                });
            }
        });
    }

    function save() {
        return $.ajax({"url": "/admin/posts", "method": "post", "dataType": "json", "data": $("form").serialize()});
    }
}(jQuery, window));
