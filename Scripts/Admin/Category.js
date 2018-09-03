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
                resetForm();
                $("#btnSubmit").val("添加");
            }
        });
        $("#position_value, #position_name").on("change", function(){
            $("#position").attr("name", $("#position_name").val()).val($("#position_value").val());
        });

        $("form").on("submit", function(){
            var def_save = $("#btnSubmit").val() == "添加" ? post() : put();;
            def_save.always(function(){
                initData();
                resetForm();
            });
            return false;
        });
        $("#btnDel").on("click", function(){
            del().done(function(){
                initData();
                resetForm();
            });
        });
    }
    function initData() {
        $("#categories").empty();
        $("#position_value option:gt(0)").remove();
        get().done(function(data, textStatus, jqXHR) {
            bindCategories(data["data"]);
            bindPosition(data["data"]);
        });
    }
    function resetForm(){
        $("form")[0].reset();
        $("#btnSubmit").val("添加");
        $("#btnDel").hide();
    }
    function bindCategories(data) {
        for(var i = 0, l = data.length; i < l; i++){
            $("#categories").append("<li></li>");
            for(var j = 1; j < data[i]["deep"]; j++) {
                $("#categories li:last").append("&nbsp;&nbsp;&nbsp;&nbsp;");
            }
            $("#categories li:last").append("<input type=\"checkbox\" value=\"" + data[i]["id"] + "\" id=\"category_" + data[i]["id"] + "\" />");
            if(data[i]["enabled"] == 1) {
                $("#categories li:last").append("<label for=\"category_" + data[i]["id"] + "\">" + data[i]["name"] + "</label>");
            }
            else {
                $("#categories li:last").append("<label for=\"category_" + data[i]["id"] + "\"><del>" + data[i]["name"] + "</del></label>");
            }
            $("#categories li:last").data("category", data[i]);
        }
    }
    function bindPosition(data) {
        for(var i = 0, l = data.length; i < l; i++){
            if(data[i]["enabled"] == 1) {
                $("#position_value").append("<option value=\"" + data[i]["id"] + "\"></option>");
                for(var j = 1; j < data[i]["deep"]; j++) {
                    $("#position_value option:last").append("&nbsp;&nbsp;&nbsp;&nbsp;");
                }
                $("#position_value option:last").append(data[i]["name"]);
            }
        }
    }

    function bindCategory(data) {
        $("#category_id").val(data["id"]);
        $("#category_name").val(data["name"]);
        $("#category_remark").val(data["remark"]);
        $("#position").val("").removeAttr("name");
        if(data["enabled"] == 1) {
            $("#btnDel").show();
        }
        else {
            $("#btnDel").hide();
        }
    }

    function get(data) {
        return $.ajax({"url": "", "dataType": "json", "method": "get", "data": {"columns": "id,name,pid,remark,lv, deep,rv,enabled", "enabled": -1, "deep": "*"}});
    }
    function post(){
        return $.ajax({"url": "", "dataType": "json", "method": "post", "data": $("form").serialize() });
    }
    function put(){
        return $.ajax({"url": "", "dataType": "json", "method": "put", "data": $("form").serialize() });
    }
    function del() {
        return $.ajax({"url": "", "dataType": "json", "method": "delete", "data": {"id": $("#category_id").val()} });
    }
}(jQuery, window));
