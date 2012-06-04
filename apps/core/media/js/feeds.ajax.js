function task_view(task) {
	task = $('<div />').html(task).text();
	// Show a dialog box with details, and editing capability
	$("#task-edit .task-title").html(task);
	$.getJSON(tp_home_url + "api/project/" + project_id + "/task/detail/?name=" + task, function(data) {
		var tskpk = data[0].pk;
		
		$("#task-pk").val(data[0].pk);
		$("#task-name").val(data[0].name);
		$("#task-description").html(data[0].description);
		$("#task-progress").val(data[0].progress);
        $("#task-milestone").val(data[0].milestone);
		$("#task-complete").attr('checked', false);
		if (data[0].progress == "100")
			$("#task-complete").attr('checked', true);
		$("#task-complete").click(function() {
			if($(this).val())
				$("#task-progress").val("100");
		});
		$("#task-type").val(data[0].type);
		$("#task-priority").val(data[0].priority);
		$("#task-status").val(data[0].status);
		$("#task-assignees").val([]);
		$("#task-assignees").val(data[0].assignees_full.split(", "));
            
        $("#task-edit").modal('show');
	});
}

$(function () {
	$('#milestone-add').modal({show: false}).find("input[type=submit]").hide();
	$('#milestone-add .btn-save').click(function() {
		var name = $('#milestone-add .name_field').val();
		var description = $('#milestone-add .description_field').val();
		var fdata = "";
		$('#milestone-add input, #milestone-add textarea').each(function() {
			fdata += $(this).attr("name") + '=' + $(this).val() + '&';
		});
		$.ajax({
			url: tp_home_url + "api/project/" + project_id + "/milestones/add/",
			type: "POST",
			data: fdata,
			success: function(data) {
				var retval = $.trim(data);
				if (retval != "Success!")
					alert(retval);
				else
					update_feeds();
				$("#milestone-add").modal('hide');
			}
		});
	});
	
	$("#task-add .btn-save").click(function(){
		$("#task-add-form").submit();
	});
	$("#task-add-form").submit(function() {
        var data_string = "";
        $.each($("#task-add-form input, #task-add-form select, #task-add-form textarea"), function() {
            data_string += $(this).attr("name") + "=" + $(this).val() + "&";
        });
        
		$.ajax({
			url: tp_home_url + "api/project/" + project_id + "/task/add/",
			type: "POST",
			data: data + "save=1",
			success: function(data) {
				$("#task-add").modal('hide');
				update_tasks_feed();
				update_all_tasks_feed();
				update_milestone_feed();
				$("input[name=csrf]").val($.trim(data));
                $.each($("#task-add-form input, #task-add-form select, #task-add-form textarea"), function() { $(this).val(""); });
			}
		});
		return false;
	});
	
	$("#task-edit .btn-save").click(function(){
		$("#task-edit-form").submit();
	});
	$("#task-edit-form").submit(function() {
		var data_string = "";
		$.each($("#task-edit-form input, #task-edit-form select, #task-edit-form textarea"), function() {
			data_string += $(this).attr("name") + "=" + $(this).val() + "&";
		});
		
		$.ajax({
			url: tp_home_url + "api/project/" + project_id + "/task/edit/",
			type: "POST",
			data: data_string + "save=1",
			success: function(data) {
				$("#task-edit").modal('hide');
				update_tasks_feed();
				update_all_tasks_feed();
				update_milestone_feed();
				$("input[name=csrf]").val($.trim(data));
                $.each($("#task-edit-form input, #task-edit-form select, #task-edit-form textarea"), function() { $(this).val(""); });
			},
		});
		return false;
	});
});
