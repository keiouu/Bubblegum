$("#deployments-add .btn-save").click(function(){
    $("#deployments-add-form").submit();
});
$("#deployments-add-form").submit(function() {
    var data_string = "";
    $.each($("#deployments-add-form input, #deployments-add-form select, #deployments-add-form textarea"), function() {
        data_string += $(this).attr("name") + "=" + $(this).val() + "&";
    });
    $.ajax({
        url: tp_home_url + "api/project/" + project_id + "/deployments/add/",
        type: "POST",
        data: data_string + "save=1",
        success: function(data) {
            
        },
    });
    return false;
});