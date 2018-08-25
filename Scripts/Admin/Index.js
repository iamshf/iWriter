;(function($, window){
    $(function(){
        initEvent();
    });

    function initEvent() {
        $("form").on("submit", function(){
            submit().done(function(){
                alert("成功");
            }).fail(function(){
                alert("失败");
            });
            return false;
        });
    }
    function submit() {
        return $.ajax({"url": "", "method": "post", "dataType": "json", "data": $("form").serialize()});
    }
}(jQuery, window));
