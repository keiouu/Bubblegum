var feed_milestone = "", feed_milstone_fk = 0;

function update_milestone_feed() {
	if ($("#milestone_feed").length > 0) {
		$.getJSON(tp_home_url + "api/project/" + project_id + "/milestones/", function(data) {
			$("#milestone_feed").html(json_to_table(data, ["name", "progress"])).find("tr td:first-child").each(function(i) {
				if ($.trim($(this).html()) != "No Data!") {
					$(this).wrapInner('<a href="#" data-mpk="'+data[i].pk+'" />').click(function() {
						feed_milestone = $(this).find("a").html();
						feed_milstone_fk = $(this).find("a").attr("data-mpk");
						$("#milestoneCollapse").collapse('hide');
						update_feeds();
						return false;
					});
				}
			});
			update_pagination($(".pagination[data-link=milestone_feed]"));
		});
	}
}

function update_tasks_feed() {
	if ($("#tasks_feed").length > 0) {
		if (typeof project_id != 'undefined')
			var url = tp_home_url + "api/project/" + project_id + "/tasks/?own_tasks_only=1";
		else
			var url = tp_home_url + "api/tasks/?own_tasks_only=1";
		if (feed_milestone.length > 0)
			url += '&milestone=' + feed_milestone;
		$.getJSON(url, function(data) {
			$("#tasks_feed").html(json_to_table(data, ["milestone", "name", "type", "priority", "status", "progress"])).find("tr td:nth-child(2)").each(function() {
				$(this).wrapInner('<a href="#" />').click(function() {
					task_view($(this).find("a").html())
					return false;
				});
			});
			if ($("#tasks_feed tr").length == 0) {
				$("#tasks_feed").append('<tr><td colspan="6">No tasks!</td></tr>');
			}
			update_pagination($(".pagination[data-link=tasks_feed]"));
		});
	}
}

function update_all_tasks_feed() {
	if ($("#all_tasks_feed").length > 0) {
		if (typeof project_id != 'undefined')
			var url = tp_home_url + "api/project/" + project_id + "/tasks/";
		else
			var url = tp_home_url + "api/tasks/";
		if (feed_milestone.length > 0)
			url += '?milestone=' + feed_milestone;
		$.getJSON(url, function(data) {
			$("#all_tasks_feed").html(json_to_table(data, ["milestone", "name", "type", "priority", "status", "progress", "assignees"]))
			                    .find("tr td:nth-child(2)")
			                    .each(function() {
				$(this).wrapInner('<a href="#" />').click(function() {
					task_view($(this).find("a").html())
					return false;
				});
			});
			if ($("#all_tasks_feed tr").length == 0) {
				$("#all_tasks_feed").append('<tr><td colspan="7">No tasks!</td></tr>');
			}
			update_pagination($(".pagination[data-link=all_tasks_feed]"));
		});
	}
}

function update_feeds() {
	update_milestone_feed();
	update_tasks_feed();
	update_all_tasks_feed();
}

$(function () {
	update_feeds();
	
	$('#milestoneCollapse').on('show', function () {
		feed_milestone = "";
		feed_milstone_fk = 0;
		update_tasks_feed();
		update_all_tasks_feed();
	});
});
