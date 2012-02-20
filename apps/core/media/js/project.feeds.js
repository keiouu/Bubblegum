function update_milestone_feed() {
	$.getJSON(tp_home_url + "api/project/" + project_id + "/milestones/",
		function(data) {
			$.each(data, function(key, val) {
				$("#milestone_feed").html(data);
			});
		}
	);
}

function update_tasks_feed() {
	$.ajax({
	  url: tp_home_url + "api/project/" + project_id + "/tasks/",
	  success: function(data) {
		 $("#tasks_feed").html(data);
	  }
	});
}

$(function () {
	update_milestone_feed();
	update_tasks_feed();
});
