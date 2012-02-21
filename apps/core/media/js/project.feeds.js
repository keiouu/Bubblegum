function json_to_table(data, headings) {
	var html = '';
	$.each(data, function(key, val) {
		html += '<tr>';
		if (key == "error") {
				html += '<td colspan="'+headings.length+'">' + val + '</td>';
		} else {
			$.each(val, function(key2, val2) {
				if (headings.indexOf(key2) > -1)
					html += '<td>' + val2 + '</td>';
			});
		}
		html += '</tr>';
	});
	return html;
}

function update_milestone_feed() {
	$.getJSON(tp_home_url + "api/project/" + project_id + "/milestones/",
		function(data) {
			$("#milestone_feed").html(json_to_table(data, ["name", "progress"]));
		}
	);
}

function update_tasks_feed() {
	$.getJSON(tp_home_url + "api/project/" + project_id + "/tasks/?own_tasks_only=1",
		function(data) {
			$("#tasks_feed").html(json_to_table(data, ["milestone", "name", "type", "priority", "progress"]));
		}
	);
}

function update_all_tasks_feed() {
	$.getJSON(tp_home_url + "api/project/" + project_id + "/tasks/",
		function(data) {
			$("#all_tasks_feed").html(json_to_table(data, ["milestone", "name", "type", "priority", "progress", "assignees"]));
		}
	);
}

$(function () {
	update_milestone_feed();
	update_tasks_feed();
	update_all_tasks_feed();
});
