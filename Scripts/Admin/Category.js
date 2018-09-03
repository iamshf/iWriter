;(function($, window){
    $(function(){
        initEvent();
        initData();
    });

    function initEvent(){
        $("#categories").on("change", "input", function(){
            if($(this).prop("checked")) {
                $("#categories input[value!='"+ $(this).val() +"']:checked").prop("checked", false);
                bindCategory($(this).parent("li").data("category"));
                $("#btnSubmit").val("修改");
            }
            else {
                $("#position_value,#position_name").removeAttr("disabled");
            }
        });
        $("#position_value, #position_name").on("change", function(){
            $("#position").attr("name", $("#position_name").val()).val($("#position_value").val());
        });

        $("form").on("submit", function(){
            alert($(this).serialize());
            return false;
        });
    }
    function initData() {
        get().done(function(data, textStatus, jqXHR) {
            bindCategories(data["data"]);
            bindPosition(data["data"]);
        });
    }
    function bindCategories(data) {
        for(var i = 0, l = data.length; i < l; i++){
            $("#categories").append("<li></li>");
            for(var j = 1; j < data[i]["deep"]; j++) {
                $("#categories li:last").append("&nbsp;&nbsp;&nbsp;&nbsp;");
            }
            $("#categories li:last").append("<input type=\"checkbox\" value=\"" + data[i]["id"] + "\" id=\"category_" + data[i]["id"] + "\" />");
            $("#categories li:last").append("<label for=\"category_" + data[i]["id"] + "\">" + data[i]["name"] + "</label>");
            $("#categories li:last").data("category", data[i]);
        }
    }
    function bindPosition(data) {
        for(var i = 0, l = data.length; i < l; i++){
            $("#position_value").append("<option value=\"" + data[i]["id"] + "\"></option>");
            for(var j = 1; j < data[i]["deep"]; j++) {
                $("#position_value option:last").append("&nbsp;&nbsp;&nbsp;&nbsp;");
            }
            $("#position_value option:last").append(data[i]["name"]);
        }
    }

    function bindCategory(data) {
        $("#category_id").val(data["id"]);
        $("#category_name").val(data["name"]);
        $("#category_remark").val(data["remark"]);
        $("#position").val("").removeAttr("name");
        data["rv"] - data["lv"] > 1 ? $("#position_value,#position_name").attr("disabled", "disabled") : $("#position_value,#position_name").removeAttr("disabled");
    }

    function get(data) {
        return $.ajax({"url": "", "dataType": "json", "method": "get", "data": {"enabled": -1, "deep": "*"}});
    }
}(jQuery, window));
