function task_view(task) {	
	// Show a dialog box with details, and editing capability
	$("#task-edit .task-title").html(task);
	$.getJSON(tp_home_url + "api/project/" + project_id + "/task/detail/?name=" + task, function(data) {
		var tskpk = data[0].pk;
		var milestone = data[0].milestone;
		
		$("#task-pk").val(data[0].pk);
		$("#task-name").val(data[0].name);
		$("#task-description").html(data[0].description);
		$("#task-progress").val(data[0].progress);
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
		
		// Show milestones
		$.getJSON(tp_home_url + "api/project/" + project_id + "/detail/", function(data) {
			var milestones = data[0].milestones;
			$("#task-milestone").html("");
			$.each(milestones, function(i, v) {
				var html = '<option value="'+i+'" ';
				if (milestone == v)
					html += ' selected="selected"';
				html += '>'+v+'</option>';
				$("#task-milestone").append(html);
			});
			
			$("#task-edit").modal('show');
		});
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
				$("#csrf-token").val($.trim(data));
			},
		});
		return false;
	});
});
