;(function($, window){
    $(function(){
        initEvent();
    });

    function initEvent() {
        $("form").on("submit", function(){
            $("#btnSubmit").val("正在登陆…").attr("disabled", "disabled");
            submit().done(function(rep_data, textStatus, jqXHR){
                location.href = rep_data["data"]["location"];
            }).fail(function(jqXHR, textStatus, err){
                $("#btnSubmit").val("登陆").removeAttr("disabled");
                alert(jqXHR.responseJSON.msg);
            });
            return false;
        });
    }
    function submit() {
        return $.ajax({"url": "", "method": "post", "dataType": "json", "data": $("form").serialize()});
    }
}(jQuery, window));
